<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('groups')->insert([
			[
				'name_id' => 1,
				'name' => 'TT',
				'group' => 'tt_bn'
			],
			[
				'name_id' => 2,
				'name' => 'BN',
				'group' => 'tt_bn'
			],

			[
				'name_id' => 1,
				'name' => 'buy',
				'group' => 'buy_sell'
			],
			[
				'name_id' => 2,
				'name' => 'sell',
				'group' => 'buy_sell'
			],

			[
				'name_id' => 1,
				'name' => 'TOD',
				'group' => 'tod_tom_spot_forward'
			],
			[
				'name_id' => 2,
				'name' => 'TOM',
				'group' => 'tod_tom_spot_forward'
			],
			[
				'name_id' => 3,
				'name' => 'spot',
				'group' => 'tod_tom_spot_forward'
			],
			[
				'name_id' => 4,
				'name' => 'forward',
				'group' => 'tod_tom_spot_forward'
			],

			[
				'name_id' => 1,
				'name' => 'interbank',
				'group' => 'interbank_sales'
			],
			[
				'name_id' => 2,
				'name' => 'sales',
				'group' => 'interbank_sales'
			],

			[
				'name_id' => 0,
				'name' => 'investasi penyertaan langsung',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 1,
				'name' => 'investasi pemberian kredit',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 3,
				'name' => 'investasi penerimaan pinjaman luar negeri',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 4,
				'name' => 'bukan investasi pembayaran pinjaman luar negeri',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 5,
				'name' => 'bukan investasi import',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 6,
				'name' => 'bukan investasi penjualan devisa hasil ekspor',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 7,
				'name' => 'investasi pembelian saham',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 8,
				'name' => 'investasi pembelian obligasi pemerintah',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 9,
				'name' => 'investasi pembelian obligasi korporasi',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 11,
				'name' => 'investasi pembelian SBI',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 13,
				'name' => 'transaksi biaya pendidikan',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 14,
				'name' => 'transaksi biaya liburan',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 16,
				'name' => 'transaksi bersifat social, sumbangan, hibah',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 17,
				'name' => 'repatriasi dan penyertaan langsung',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 18,
				'name' => 'repatriasi keuntungan pemberian kredit',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 19,
				'name' => 'repatriasi dana hasil penjualan saham',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 20,
				'name' => 'repatriasi dana hasil penjualan obligasi pemerintah',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 21,
				'name' => 'repatriasi dana hasil penjualan obligasi korporasi',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 22,
				'name' => 'dana hasil penjualan SBI',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 23,
				'name' => 'repatriasi dividen dan kupon',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 24,
				'name' => 'disimpan dalam rekening valas dalam negeri',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 25,
				'name' => 'untuk transaksi antarbank dalam rangka trading',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 26,
				'name' => 'untuk transaksi antarbank dalam rangka cover posisi nasabah kepada bank didalam negeri',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 27,
				'name' => 'untuk transaksi antarbank dalam rangka cover posisi nasabah kepada bank luar negeri atau pihak luar',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 28,
				'name' => 'untuk kegiatan remittance',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 29,
				'name' => 'untuk transaksi valuta asing yang dilakukan oleh bank dengan nasabah tanpa underlying',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 30,
				'name' => 'untuk biaya overhead',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 31,
				'name' => 'untuk biaya administrasi',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 32,
				'name' => 'untuk kegiatan pedagang valuta asing (PVA)',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 33,
				'name' => 'untuk pembayaran pajak',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 34,
				'name' => 'untuk pembayaran hutang',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 35,
				'name' => 'untuk penambahan modal kerja',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 36,
				'name' => 'untuk biaya remunerasi pegawai',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 37,
				'name' => 'untuk pembelian barang',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 38,
				'name' => 'untuk penjualan barang',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 39,
				'name' => 'untuk pembelian jasa',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 40,
				'name' => 'untuk penjualan jasa',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 41,
				'name' => 'untuk pencairan bunga dan/atau pokok dari penempatan pada rekening valas dalam negeri',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 42,
				'name' => 'untuk repatriasi atas penghasilan dari jasa yang dilakukan didalam negeri',
				'group' => 'lhbu_remarks_code'
			],
            [
				'name_id' => 43,
				'name' => 'untuk lindung nilai atas kepemilikan data valas',
				'group' => 'lhbu_remarks_code'
			],

            [
				'name_id' => 1,
				'name' => 'foto copy pemberitahuan impor barang (PIB)',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 2,
				'name' => 'foto copy pemberitahuan expor barang (PEB)',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 3,
				'name' => 'letter of credit (L/C)',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 4,
				'name' => 'invoice/commercial invoice',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 5,
				'name' => 'list of invoice',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 6,
				'name' => 'dokumen pembayaran biaya sekolah di luar negeri, antara lain : perkiraan kebutuhan biaya sekolah dan biaya hidup di luar negeri',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 7,
				'name' => 'dokumen pembayaran biaya berobat ke luar negeri, antara lain : perkiraan kebutuhan berobat dan akomodasi',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 8,
				'name' => 'dokumen biaya perjalanan luar negeri, antara lain : berupa perkiraan kebutuhan biaya perjalanan dan akomodasi',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 9,
				'name' => 'dokumen pembayaran atas penggunaan jasa konsultan luar negeri, antara lain : fotocopy kontrak jasa konsultan',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 10,
				'name' => 'dokumen pembayaran jasa tenaga asing di indonesia, antara lain : fotocopy surat perjanjian kerja atau dokumen pendukung lain antara tenaga kerja asing yang bersangkutan dengan badan usaha',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 11,
				'name' => 'fotocopy loan/credit agreement atau dokumen utang lainnya (termasuk promissory note)',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 12,
				'name' => 'surat ijin KUPVA dari bank indonesia dan laporan historical turnover yang menunjukkan net jual KUPVA kepada nasabah',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 13,
				'name' => 'dokumen proyeksi cashflow untuk kegiatan usaha jasa travel agent',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 14,
				'name' => 'SWIFT message',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 15,
				'name' => 'tested telex',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 16,
				'name' => 'tested fax',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 17,
				'name' => 'RDMS deal conversation/bloomberg ticket',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 18,
				'name' => 'bukti tagihan pajak',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 19,
				'name' => 'bukti tagihan atas kewajiban pembayaran listrik, telpon, air',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 20,
				'name' => 'surat perjanjian kerja',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 21,
				'name' => 'SKBDN (surat kredit berdokumen dalam negeri)',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 22,
				'name' => 'bukti divestasi penyertaan langsung',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 23,
				'name' => 'bukti pembelian/penjualan saham',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 24,
				'name' => 'bukti pembagian deviden (termasuk hasil RUPS dan dokumen lainnya yang menggambarkan besarnya nominal pembagian deviden)',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 25,
				'name' => 'bukti pembayaran kupon',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 26,
				'name' => 'bukti pembelian/penjualan obligasi korporasi termasuk produk reksadana dan KIK',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 27,
				'name' => 'bukti pembelian/penjualan SBN',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 28,
				'name' => 'bill of lading',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 29,
				'name' => 'purchase agreement',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 30,
				'name' => 'sales agreement/sales contract',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 31,
				'name' => 'surat perjanjian kredit',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 32,
				'name' => 'wesel',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 33,
				'name' => 'faktur transaksi jual beli barang',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 34,
				'name' => 'faktur transaksi jual beli jasa',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 35,
				'name' => 'nota debit (debit note)',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 36,
				'name' => 'fotokopi perjanjian royalty (royalty agreement)',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 37,
				'name' => 'akta jual beli dan bukti kepemilikan pihak asing atas aset terkait dengan penjualan aset di indonesia yang dimiliki oleh pihak asing',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 38,
				'name' => 'dokumen penjualan valuta asing terhadap rupiah yang berasal dari penjualan valuta asing hasil ekspor',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 39,
				'name' => 'dokumen proyeksi cashflow untuk kegiatan perdagangan internasional (ekspor-impor)',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 40,
				'name' => 'purchase order/dokumen pembelian lain yang telah dikonfirmasi oleh penjual',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 41,
				'name' => 'bukti pembelian/penjualan surat berharga lainnya',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 42,
				'name' => 'bukti pembagian hasil investasi lainnya',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 43,
				'name' => 'surat permintaan penyetoran rekening saldo atas transaksi tertentu yang dipersyaratkan oleh otoritas yang berwenang',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 44,
				'name' => 'bukti keikutsertaan dalam tender dan penyediaan jaminan/bank garansi',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 45,
				'name' => 'dokumen proyeksi arus kas yang terkait dengan suatu proyek tertentu',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 46,
				'name' => 'perjanjian pembukaan vostro pihak asing dengan bank untuk tujuan remitansi, MT 299, atau MT 599 yang berisi pernyataan dari bank koresponden bahwa dana yang ada akan dipergunakan untuk tujuan remitansi ke indonesia',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 47,
				'name' => 'dokumen yang memberikan informasi kebutuhan valuta asing untuk tujuan remitansi ke indonesia',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 48,
				'name' => 'proyeksi arus kas yang dikeluarkan oleh pihak asing untuk tujuan pembayaran beban operasional dalam mata uang rupiah',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 49,
				'name' => 'settlement agreement',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 50,
				'name' => 'dokumen waris',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 51,
				'name' => 'memorandum of understanding dan/atau agreement dalam rangka pembelian dan penjualan aset di dalam negeri melalui merger dan akuisisi',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 52,
				'name' => 'dokumen estimasi mengenai hasil investasi yang akan diterima',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 998,
				'name' => 'tanpa underlying',
				'group' => 'lhbu_remarks_kind'
			],
            [
				'name_id' => 999,
				'name' => 'dengan underlying lainnya',
				'group' => 'lhbu_remarks_kind'
			],
		]);
    }
}
