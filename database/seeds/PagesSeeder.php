<?php

use Illuminate\Database\Seeder;
use App\Pages;
use App\PageContents;
class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pages::truncate();
        DB::statement('ALTER SEQUENCE pages_id_seq RESTART WITH 1');
        $data = [
            [
                'name'=>'home',
                'parent_id'=>'0',
                'type'=>'page',
            ],[
                'name'=>'guide',
                'parent_id'=>'0',
                'type'=>'page',
            ],[
                'name'=>'user_manual',
                'parent_id'=>'2',
                'type'=>'page',
            ],[
                'name'=>'integrity_pact',
                'parent_id'=>'2',
                'type'=>'page',
            ],[
                'name'=>'terms_conditions',
                'parent_id'=>'2',
                'type'=>'page',
            ],[
                'name'=>'procedure',
                'parent_id'=>'0',
                'type'=>'page',
            ],[
                'name'=>'registration',
                'parent_id'=>'6',
                'type'=>'page',
            ],[
                'name'=>'qualification',
                'parent_id'=>'6',
                'type'=>'page',
            ],[
                'name'=>'buying',
                'parent_id'=>'6',
                'type'=>'page',
            ],[
                'name'=>'timas',
                'parent_id'=>'6',
                'type'=>'page',
            ],[
                'name'=>'announcement',
                'parent_id'=>'0',
                'type'=>'page',
            ],[
                'name'=>'open',
                'parent_id'=>'11',
                'type'=>'page',
            ],[
                'name'=>'tender',
                'parent_id'=>'11',
                'type'=>'page',
            ],[
                'name'=>'contact',
                'parent_id'=>'0',
                'type'=>'page',
            ],
        ];
        Pages::insert($data);
        PageContents::truncate();
        DB::statement('ALTER SEQUENCE page_contents_id_seq RESTART WITH 1');
        $data = [
            [
                'page_id'=>'1',
                'language'=>'en',
                'title'=>'Home',
                'content'=>'
                    <h1 style="text-align:center;font-size:25px;"><b>Welcome</b></h1>
                    <h2 style="text-align: center; "><b><span style="font-size: 25px;">E-Procurement PT Timas Suplindo</span></b></h2>
                    <br><br>
                    <p style="text-align:center;"><img class="img-responsive img-responsive-table" src="https://www.timas.com/timasisadmin/source/NATIVE%20MILESTONE.jpg" width="750" height="400"></p>
                    
                    <p style="text-align: justify;">Founded in 1989 as a fabricator and erector of mechanical&nbsp; equipment company, Timas&nbsp;has now transformed and established as one of Indonesian leading onshore &amp; offshore EPCIC contractor. With decades of experiences, we are able to deliver the most promising outcome and are always a step ahead from our competitors.</p><p>
                    
                    </p><p style="text-align: justify;">Supported by more than 1000 professionals, we are capable of overcoming any challenge, obstacles and limitations in the EPCIC industry. Until today, we always challenge ourselves to hold our prominent position amongst major EPICIC companies.</p>
                    
                    <br><br>
                    
                    <h3 style="text-align:center;font-size:20px;"><b>Vision</b></h3>
                    <p style="text-align:center;font-size:18px;">To Become a Global EPCIC Company</p>
                    
                    <h3 style="text-align:left;font-size:20px;"><b>Misions</b></h3>
                    <ul>
                    <li>Maintain customer\'s trust in a more sustainable path</li>
                    <li>Focus on human resources management development</li>
                    <li>Implement a professional and measurable corporate governance</li>
                    <li>Deliver added value to stakeholders</li>
                    </ul>
                    
                    <h3 style="text-align:left;font-size:20px;"><b>Core Values</b></h3>
                    <table style="width: auto;">
                    <tbody>
                    <tr>
                    <td style="width: 61px;"><strong><img src="https://www.timas.com/timasisadmin/source/T.png" width="58" height="54" class="img-responsive-table"></strong></td>
                    <td style="width: 783.667px;">
                    <p><strong>TEAMWORK</strong>&nbsp;-&nbsp;We value our colleagues and enjoy working collaboratively as a team to achieve more and deliver excellent product and service.</p>
                    </td>
                    </tr>
                    
                    <tr>
                    <td style="width: 61px;"><strong><img src="https://www.timas.com/timasisadmin/source/I.png" width="58" height="54" class="img-responsive-table"></strong></td>
                    <td style="width: 783.667px;">
                    <p><strong>INTEGRITY</strong>&nbsp;-&nbsp;We act and communicate with the highest standard of individual and corporate integrity</p>
                    </td>
                    </tr>
                    
                    <tr>
                    <td style="width: 61px;"><strong><img src="https://www.timas.com/timasisadmin/source/M.png" width="58" height="54" class="img-responsive-table"></strong></td>
                    <td style="width: 783.667px;">
                    <p><strong>MEASURABLE</strong>&nbsp;-&nbsp;We are committed to continuously improve our measurable goals for the benefit of our client’s satisfaction</p>
                    </td>
                    </tr>
                    
                    <tr>
                    <td style="width: 61px;"><strong><img src="https://www.timas.com/timasisadmin/source/A.png" width="58" height="54" class="img-responsive-table"></strong></td>
                    <td style="width: 783.667px;">
                    <p><strong>ACCOUNTABLE</strong>&nbsp;-&nbsp;We take full responsibility for our actions and are committed to deliver the best possible outcome for our stakeholders and customers.</p>
                    </td>
                    </tr>
                    
                    <tr>
                    <td style="width: 61px;"><strong><img src="https://www.timas.com/timasisadmin/source/S.png" width="58" height="54" class="img-responsive-table"></strong></td>
                    <td style="width: 783.667px;">
                    <p><strong>SAFETY</strong>&nbsp;-&nbsp;We are dedicated in creating a safe workplace environment</p>
                    </td>
                    </tr>
                    
                    </tbody>
                    </table>
                ',
            ],
            [
                'page_id'=>'3',
                'language'=>'en',
                'title'=>'User Manual',
                'content'=>'
<span style="color: rgb(0, 0, 0); font-family: Arial; font-size: 13px; white-space: pre-wrap;"><a href="../storage/guides/Scope of Supply (SoS) Barang dan Jasa.pdf" target="_blank">Scope of Supply (SoS) Barang dan Jasa.pdf</a>

<a href="../storage/guides/SKI P01 10 EP 11 Negosiasi vendor version.pdf" target="_blank">SKI P01 10 EP 11 Negosiasi vendor version.pdf</a>
                    
<a href="../storage/guides/SKI P01 10 EP 04 Tender Announcement.pdf" target="_blank">SKI P01 10 EP 04 Tender Announcement.pdf</a>
                    
<a href="../storage/guides/SKI P01 10 EP 05 Prequalification vendor version.pdf" target="_blank">SKI P01 10 EP 05 Prequalification vendor version.pdf</a>
                    
<a href="../storage/guides/SKI P01 10 EP 10 Pengumuman Pemenang versi vendor.pdf" target="_blank">SKI P01 10 EP 10 Pengumuman Pemenang versi vendor.pdf</a>
                    
<a href="../storage/guides/SKI P01 10 EP 01 Vendor Registration versi vendor .pdf" target="_blank">SKI P01 10 EP 01 Vendor Registration versi vendor .pdf</a>
                    
<a href="../storage/guides/SKI P01 10 EP 06 Respon Penawaran.pdf" target="_blank">SKI P01 10 EP 06 Respon Penawaran.pdf</a></span>
                '
            ],
            [
                'page_id'=>'4',
                'language'=>'en',
                'title'=>'Integrity Pact',
                'content'=>'
                <span style="color: rgb(0, 0, 0); font-family: Arial; font-size: 13px; white-space: pre-wrap;"><a href="../storage/guides/TIMAS_INTEGRITY_PACT.pdf" target="_blank">Integrity Pact</a></span>                
                '
            ],
            [
                'page_id'=>'5',
                'language'=>'en',
                'title'=>'Terms and Conditions',
                'content'=>'
                <span style="color: rgb(0, 0, 0); font-family: Arial; font-size: 13px; white-space: pre-wrap;"><a href="../storage/guides/TIMAS_TERMS_AND_CONDITIONS.pdf" target="_blank">Terms and Conditions</a></span>
                '
            ],
            [
                'page_id'=>'14',
                'language'=>'en',
                'title'=>'CONTACT',
                'content'=>'
                <b>Address:</b><br>
                Graha TIMAS Jl. Tanah Abang II<br>
                No. 81 Jakarta 10160, Indonesia<br>
                <br>
                Email: <a href="mailto:eproc@timas.com" target="_blank">eproc@timas.com</a>
                
                 Phone: +62 21 352 2828
                '
            ],
            [
                'page_id'=>'7',
                'language'=>'en',
                'title'=>'Registration Procedure',
                'content'=>'
                <p style="text-decoration:underline"><b>Prosedur Registrasi [TIMAS WILL SEND THE REGISTRATION PROCEDURE - BELOW ARE SAMPLE FROM Other)</b></p>
                <p>PT. Timas Suplindo mengumumkan peraturan mengenai aktifitas permintaan barang/jasa yang harus diprioritaskan dengan membeli langsung dari pabrik, distributor resmi atau agen tunggal untuk mencegah terjadinya subcontract yang memiliki nilai yang kurang dalam pengirimannya. Oleh sebab itu, PT. Timas Suplindo harus mengetahui sertifikat pendaftaran vendor (Tanda Daftar Rekanan/ Surat Lulus Kualifikasi).</p>
                <p>PT. Timas Suplindo menggunakan Aturan dan Persyaratan untuk setiap usernya. Untuk User yang telah mendaftar dan menyetujui melalui PT. Timas Suplindo ePRO secara sah akan tergabung dengan aturan dan persyaratan dibawah ini:</p>
                
                <p>Memenuhi Persyaratan Administratif Seperti:
                </p><ol>
                <li>Patuh terhadap Peraturan dan Regulasi Pemerintah dalam melakukan bisnis barang dan Jasa.</li>
                <li>Memiliki Keanggotaan , Kemampuan teknik dan Manajerial sebagai penyedia barang dan jasa.</li>
                <li>Tidak dalam Penyelidikan hukum, bangkrut, dan aktifitas bisnis yang tidak berjalan atau pemimpin perusahaan yang pernah terlibat dalam masalah hukum.</li>
                <li>Secara Hukum memiliki kemampuan dalam menandatangani kontrak.</li>
                <li>Harus melunasi kawajibannya dalam membayar pajak , bukti pembayaran dapat dilakukan dengan menyertakan salinan nota pajak Tahunan (SPT), Bukti Pajak Penerimaan tahun lalu (PPH), dan salinan Surat Setoran Pajak (SSP) PPH bag. 29</li>
                <li>Memiliki SDM, Modal, Peralatan , dan fasilitas kepemilikan lainnya yang dibutuhkan dalam membelanjakan barang dan jasa.</li>
                <li>Tidak Terdapat dalam Daftar Perusahaan yang bermasalah / Blacklist.</li>
                <li>Memiliki Alamat yang jelas dan tetap , juga dapat dicapai melalui kotak surat (Post).</li></ol><p></p>
                <p><span style="color: rgb(0, 0, 0); font-family: Arial; white-space: pre-wrap; font-size: 10.5px; text-decoration-skip-ink: none;">Untuk penyalur barang dan jasa, persyaratan yang harus dipenuhi sama kecuali Nomor 6.
                
                </span></p><p>Memenuhi Persyaratan teknik seperti:
                </p><ol>
                <li>Keterangan Mengenai Fasilitas Perusahaan yang dimiliki.</li>
                <li>Keterangan mengenai data yang digunakan dan Kapasitas Kemampuan dalam berproduksi.</li>
                <li>Jumlah SDM (Sumber Daya Manusia) dan Kualitasnya.</li>
                <li>Inovatif dan Teknologi.</li>
                <li>Pengiriman.</li>
                <li>Quality control and SOP.</li>
                <li>Sertifikat Lainnya (Jika Dibutuhkan).</li>
                <li>Memiliki kemampuan dan pengalaman dalam penanganan kerja di lapangan.</li>
                </ol><p></p>
                <p>Semua klien akan di saring melalui kriteria teknik dan administratif. dalam hal ini, PT. Timas Suplindo berhak untuk melakukan pemeriksaan dan penilaian secara langsung mengenai fasilitas yang dimiliki oleh klien. Semua Keputusan yang dibuat oleh PT. Timas Suplindo bersifat absolut dan tidak bisa diganggu - gugat juga tidak ada kewajiban kepada PT. Timas Suplindo untuk memberikan penjelasan lengkap atas keputusan yang telah dibuat.</p>
                
                <p>PT. Timas Suplindo memiliki hak dan peraturan untuk menentukan jumlah vendor dan juga keberadaan vendor di dalam daftar vendor PT. Timas Suplindo yang akan di evaluasi secara rutin. dalam hal penanganan ini, PT. Timas Suplindo tidak akan mengeluarkan dokumen lagi.</p>
                
                <p>Surat Pengajuan kandidat klien PT. Timas Suplindo sebagai klien PT. Timas Suplindo , merupakan hasil persetujuan dari semua klien berdasarkan aturan dan persyaratan dari PT. Timas Suplindo.</p>
                '
            ],
        ];
        PageContents::insert($data);
    }
}
