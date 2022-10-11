<?php

namespace App\Http\Controllers;


//use DateTime;
//use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FcdController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        if(!in_array('TRS001', explode(',', auth()->user()->trustee))){
            abort(404);
        }
        return view('ftp-curve-data.index');
    }

    var $_fcdColumn=array("id", "id_upload", "note", "created_by", "created_at", "updated_by", "updated_at");
    function fcdTable(Request $request){
        $columns=$this->_fcdColumn;
        $querynya=DB::table('curve_data_generated');
        $totalData = $querynya->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if (empty($request->input('search.value'))) {
            $posts = $querynya->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $posts = $querynya->where(function ($query) use ($search) {
                $arraycolumn = $this->_fcdColumn;
                foreach ($arraycolumn as $name_column) {
                    $query->orWhere($name_column, 'LIKE', "%{$search}%");
                }
            })->offset($start)->limit($limit)->orderBy($order, $dir)->get();
            $totalFiltered = $querynya->where(function ($query) use ($search) {
                $arraycolumn = $this->_fcdColumn;
                foreach ($arraycolumn as $name_column) {
                    $query->orWhere($name_column, 'LIKE', "%{$search}%");
                }
            })->count();
        }
        return array(
            "draw" => (int)$request->input('draw'),
            "recordsTotal" => (int)$totalData,
            "recordsFiltered" => (int)$totalFiltered,
            "data" => $posts
        );
    }

    function fcdUpload(Request $re){
        function temporaryQry(){
            return DB::table('ftp_curve_data')->where('id_upload', 0)->where('created_by', str_replace('@idn.ccb.com','', auth()->user()->email));
        }
        temporaryQry()->delete();
        Storage::deleteDirectory('upload-excel');
        $inputFileName=storage_path('app/').$re->fileName->store('upload-excel');

        /*$inputFileType = IOFactory::identify($inputFileName);
        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);*/
        $spreadsheet = IOFactory::load($inputFileName);

        $id_upload= uniqid('', false);
        $result=array();
        $listTabSheet=array(
            array(
                'format'=>0,//ON IDR (Indonia)
                'indexTab'=>array(0),
            ),
            array(
                'format'=>1,//JIBOR Format
                'indexTab'=>array(1,7,8,9,11,15,17),
            ),
            array(
                'format'=>2,//ID. Sov. Format
                'indexTab'=>array(2),
            ),
            array(
                'format'=>3,//BI TD Valas Format
                'indexTab'=>array(3),
            ),
            array(
                'format'=>4,//US T's Format
                'indexTab'=>array(4,6,10,12,14,16,18,20),
            ),
            array(
                'format'=>5,//SGD Depo Format
                'indexTab'=>array(5),
            ),
            array(
                'format'=>6,//HK Sov. Format
                'indexTab'=>array(13, 19),
            ),
        );
        function isTimestamp($timestamp) {
            return ctype_digit($timestamp) && strtotime(date('Y-m-d H:i:s', $timestamp)) === (int)$timestamp;
        }
        function isValidDate($myDateString){
            return (bool)strtotime($myDateString);
        }
        function convertDt($time='00:00'){
            if(is_string($time)) {//if string means hour or date string
                if (strlen($time) < 6) {//if hour
                    return date('Y') . '-' . $_POST['monthFile'] . '-01' . ' ' . $time;
                }
                return date('Y-m-d', strtotime($time));
            }
            if($time>1){//if exel timestamp more than one means date
                return date('Y-m-d', Date::excelToTimestamp($time));
            }
            return date('Y').'-'.$_POST['monthFile'].'-01'.' '.date('H:i', Date::excelToTimestamp($time));//hour excel timestamp
            //return date('Y').'-'.$_POST['monthFile'].'-01'.' '.date('H:i', strtotime($time));
            //string date
            /*if(isValidDate($time)){
                if(strtotime($time)>100){
                    return date('Y-m-d', strtotime($time));
                }
                return date('Y').'-'.$_POST['monthFile'].'-01'.' '.date('H:i', Date::excelToTimestamp($time));
            }
            return date('Y').'-'.$_POST['monthFile'].'-01'.' '.date('H:i', strtotime($time));*/
        }

        /*function convertDt($time='00:00'){
            if(strlen($time)===5){//hour
                return date('Y').'-'.$_POST['monthFile'].'-01'.' '.$time;
            }
            return date('Y-m-d', strtotime($time));
        }*/
        function insertToFtpCurveData($curv_id,$curv_dt,$tm_val,$curv_val,$curv_nm,$ccycd, $intar_cgycd){
            $replacements =array(
                'wk' => 'w',
                'mo' => 'm',
                'on' => '1d',
                'o/n' => '1d',
            );
            $tm_val=strtr(strtolower($tm_val), $replacements);
            $IntAr_CgyCd['ACT/360']='01';
            $IntAr_CgyCd['ACT/ACT']='02';
            $IntAr_CgyCd['ACT/365']='03';
            $IntAr_CgyCd['30/360']='04';
            $IntAr_CgyCd['30/365']='05';
            $IntAr_CgyCd['30/ACT']='06';
            $dataInsert = array(
                'curv_id' => $curv_id,
                'curv_dt' => convertDt($curv_dt),
                'tm_val' => substr_replace($tm_val, '', -1),
                'tm_uncd' => strtoupper(substr($tm_val, -1)),
                'curv_val' => $curv_val,//$curv_val
                'curv_nm' => $curv_nm,
                'ccycd' => $ccycd,
                'intar_cgycd' => $IntAr_CgyCd[$intar_cgycd],
                'created_by' => str_replace('@idn.ccb.com','', auth()->user()->email),
                'created_at' => date('Y-m-d H:i:s'),
                'id_upload' => 0,
            );
            DB::table('ftp_curve_data')->insert($dataInsert);
            return $dataInsert;
            /*try {

            }catch(QueryException $e){
                DB::table('ftp_curve_data')->where('id_upload', '=', 0)->delete();
                return response()->json(['message' => $e], 500);
            }*/
        }
        $parseDataFormat['0'] = function($dataMain, $sheetName) {
            //return array();
            $resultFunction=array();
            foreach ($dataMain as $key => $val){
                $tm_val=explode(' ',$dataMain[0][0])[1]??$dataMain[0][0];
                $intar=$dataMain[7][5];
                if($key>7){//add one
                    $resultFunction[]=insertToFtpCurveData(
                        uniqid('', false),
                        $val[0],
                        $tm_val,
                        $val[1],
                        $sheetName,
                        'CNY',
                        $intar
                    );
                }
            }
            return $resultFunction;
        };
        $parseDataFormat['1'] = function($dataMain, $sheetName) {
            //return array();
            $resultFunction=array();
            foreach ($dataMain as $key => $val){
                if($key!==0)
                    $resultFunction[]=insertToFtpCurveData(
                        uniqid('', false),
                        $val[5],
                        $val[0],
                        $val[3],
                        $sheetName,
                        'CNY',
                        $val[6]
                    );
            }
            return $resultFunction;
        };
        $parseDataFormat['2'] = function($dataMain, $sheetName) {
            //return array();
            $resultFunction=array();
            foreach ($dataMain as $key => $val){
                if($key!==0)
                    $resultFunction[]=insertToFtpCurveData(
                        uniqid('', false),
                        $val[6],
                        $val[0],
                        $val[4],
                        $sheetName,
                        'CNY',
                        $val[8]
                    );
            }
            return $resultFunction;
        };
        $parseDataFormat['3'] = function($dataMain, $sheetName) {
            //return array();
            $resultFunction=array();
            foreach ($dataMain as $key => $val){
                if($key!==0)
                    $resultFunction[]=insertToFtpCurveData(
                        uniqid('', false),
                        $val[4],
                        $val[0],
                        $val[2],
                        $sheetName,
                        'CNY',
                        $val[6]
                    );
            }
            return $resultFunction;
        };
        $parseDataFormat['4'] = function($dataMain, $sheetName) {
            //return array();
            $resultFunction=array();
            foreach ($dataMain as $key => $val){
                if($key!==0)
                    $resultFunction[]=insertToFtpCurveData(
                        uniqid('', false),
                        $val[6],
                        $val[0],
                        $val[4],
                        $sheetName,
                        'CNY',
                        $val[7]
                    );
            }
            return $resultFunction;
        };
        $parseDataFormat['5'] = function($dataMain, $sheetName) {
            //return array();
            $resultFunction=array();
            foreach ($dataMain as $key => $val){
                $tm_val1=explode(' ',$dataMain[0][0])[1];
                $tm_val2=explode(' ',$dataMain[0][4])[1];
                $intar=$dataMain[10][9];
                if($key>10){//add one
                    $resultFunction[]=insertToFtpCurveData(
                        uniqid('', false),
                        $val[0],
                        $tm_val1,
                        $val[1],
                        $sheetName,
                        'CNY',
                        $intar
                    );

                    $resultFunction[]=insertToFtpCurveData(
                        uniqid('', false),
                        convertDt($val[4]),
                        $tm_val2,
                        $val[5],
                        $sheetName,
                        'CNY',
                        $intar
                    );
                }
            }
            return $resultFunction;
        };
        $parseDataFormat['6'] = function($dataMain, $sheetName) {
            //return array();
            $resultFunction=array();
            foreach ($dataMain as $key => $val){
                $tm_val1=explode(' ',$dataMain[0][0])[1];
                $tm_val2=explode(' ',$dataMain[0][3])[1];
                $tm_val3=explode(' ',$dataMain[0][6])[1];
                $intar=$dataMain[10][10];
                if($key>10){// add one
                    $resultFunction[]=insertToFtpCurveData(
                        uniqid('', false),
                        $val[0],
                        $tm_val1,
                        $val[1],
                        $sheetName,
                        'CNY',
                        $intar
                    );

                    $resultFunction[]=insertToFtpCurveData(
                        uniqid('', false),
                        $val[3],
                        $tm_val2,
                        $val[4],
                        $sheetName,
                        'CNY',
                        $intar
                    );

                    $resultFunction[]=insertToFtpCurveData(
                        uniqid('', false),
                        $val[6],
                        $tm_val3,
                        $val[7],
                        $sheetName,
                        'CNY',
                        $intar
                    );
                }
            }
            return $resultFunction;
        };
        $listSheetName=$spreadsheet->getSheetNames();
        foreach ($listTabSheet as $val){
            $format=$val['format'];
            foreach ($val['indexTab'] as $tab){
                $collectData=$spreadsheet->getSheet($tab)->toArray(null, false, false);//excel to array specific sheet
                $sheetName=$listSheetName[$tab];
                $parse=$parseDataFormat[$format]($collectData, $sheetName);//parsing data based on sheet format
                if($parse!==null) {
                    $result = array_merge($parse, $result);
                }
            }
        }
        DB::table('curve_data_generated')->insert( array(
            'id' => uniqid('', false),
            'id_upload' =>  $id_upload,
            'note' => $re->note,
            'created_by' => str_replace('@idn.ccb.com','', auth()->user()->email),
            'created_at' => date('Y-m-d H:i:s'),
        ));
        temporaryQry()->update(array('id_upload' => $id_upload));
        Storage::deleteDirectory('upload-excel');
        return $this->convertExcel(0,$result);
    }

    function convertExcel($id_upload=0,$data=0){
        if($data===0){
            $data = DB::table('ftp_curve_data')->where('id_upload', '=', $id_upload)->get();
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BATCH_TENANCY_ID');
        $sheet->setCellValue('B1', 'VRTL_TENANCY_ID');
        $sheet->setCellValue('C1', 'CURV_ID');
        $sheet->setCellValue('D1', 'CURV_DT');
        $sheet->setCellValue('E1', 'TM_VAL');
        $sheet->setCellValue('F1', 'TM_UNCD');
        $sheet->setCellValue('G1', 'SEQ_NO');
        $sheet->setCellValue('H1', 'CURV_VAL');
        $sheet->setCellValue('I1', 'CURV_CD');
        $sheet->setCellValue('J1', 'CURV_NM');
        $sheet->setCellValue('K1', 'CCYCD');
        $sheet->setCellValue('L1', 'INTAR_CGYCD');
        $row=2;
        foreach ($data as $index => $value) {
            if($id_upload===0){
                $value=(object)$value;
            }
            $sheet->setCellValue("A$row", $value->batch_tenancy_id??'');
            $sheet->setCellValue("B$row", $value->vrtl_tenancy_id??'');
            /*$sheet->setCellValue("C$row", $value->curv_id);
            $sheet->getCell("C$row")->setValueExplicit($value->curv_id, DataType::TYPE_STRING);*/
            $sheet->setCellValue("D$row", $value->curv_dt);
            $sheet->setCellValue("E$row", $value->tm_val);
            $sheet->setCellValue("F$row", $value->tm_uncd);
            $sheet->setCellValue("H$row", $value->curv_val);
            $sheet->getCell("H$row")->setValueExplicit($value->curv_val, DataType::TYPE_STRING);
            $sheet->setCellValue("J$row", $value->curv_nm);
            $sheet->setCellValue("K$row", $value->ccycd);
            $sheet->setCellValue("L$row", $value->intar_cgycd);
            $sheet->getCell("L$row")->setValueExplicit($value->intar_cgycd, DataType::TYPE_STRING);

            $row++;
        }
        $outputFileName = "FTP CURVE ALL DATA.xlsx";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$outputFileName . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        try {
            $writer->save('php://output');
        } catch (Exception $e) {
            return $e;
        }
    }

    function fcdDelete(Request $re){
        DB::table('curve_data_generated')->where('id_upload', '=', $re->id_upload)->delete();
        DB::table('ftp_curve_data')->where('id_upload', '=', $re->id_upload)->delete();
        return array('status'=>'success', 'code' => 200);
    }
}
