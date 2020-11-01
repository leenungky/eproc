<div class="tab-body">
    <div class="has-footer has-tab" style="padding: 0">
        @if($statusProcess  == 'registration')
        <div class="alert alert-info alert-flat no-margin" role="alert" @if($tender->visibility_bid_document != 'PUBLIC') hidden @endif>
            <p>{{__('tender.process.info_pre_qualification')}}</p>
        </div>
        @elseif($statusProcess  == 'started-3')
        <div class="alert alert-info alert-flat no-margin" role="alert" @if($tender->visibility_bid_document != 'PUBLIC') hidden @endif>
            <p>{{__('tender.process.info_pre_qualification2')}}</p>
        </div>
        @elseif($statusProcess  == 'started-4')
        <div class="alert alert-info alert-flat no-margin" role="alert" @if($tender->visibility_bid_document != 'PUBLIC') hidden @endif>
            <p>{{__('tender.process.info_pre_com2')}}</p>
        </div>
        @elseif($statusProcess  == 'opened-3')
        <div class="alert alert-info alert-flat no-margin" role="alert" @if($tender->visibility_bid_document != 'PUBLIC') hidden @endif>
            <p>{{__('tender.process.info_pre_tc3')}}</p>
        </div>
        @elseif($statusProcess  == 'opened-4')
        <div class="alert alert-info alert-flat no-margin" role="alert" @if($tender->visibility_bid_document != 'PUBLIC') hidden @endif>
            <p>{{__('tender.process.info_pre_com3')}}</p>
        </div>
        @else
        <div class="alert alert-info alert-flat no-margin" role="alert">
            <h6>PERHATIAN</h6>
            <p>Penyedia harus memberikan penawaran sesuai dengan parameter tender untuk metode pemasukan penawaran:</p>
            <ul>
                <li>Untuk 1 Sampul terdiri dari Administratif, Teknikal& Komersil</li>
                <li>Untuk 2 Sampul terdiri dari :
                    <ol>
                        <li>Administratif dan Teknikal</li>
                        <li>Komersil</li>
                    </ol>
                </li>
                <li>Untuk 2 Tahap
                    <ol>
                        <li>Tahap 1 Administratif dan Teknikal</li>
                        <li>Tahap 2 Komersil</li>
                    </ol>
                </li>
            </ul>
            <p>Kesalahan atau alpa pada pengisian penawaran akan menyebabkan penawaran tidak diterima dan penyedia tidak dapat mengikuti proses berikutnya</p>
        </div>
        <div class="alert alert-warning alert-flat no-margin" role="alert">
            <h6>PENTING</h6>
            <p>Pastikan kedua bagian dibawah ini telah diisi dengan lengkap dan bertuliskan "Submitted" dengan warna hijau</p>
            <ul>
                <li>Untuk 1 Sampul : Administratif, Teknikal& Komersil</li>
                <li>Untuk 2 Sampul dan 2 Tahap :
                    <ol>
                        <li>Administratif dan Teknikal</li>
                        <li>Komersil</li>
                    </ol>
                </li>
            </ul>
            <p>Dengan cara klik pada tombol Submit setelah selesai melengkapi penawaran</p>
        </div>
        @endif

        <div id="card-schedule" class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <div class="card-header-left">
                    <span class="heading-title">{{__('tender.process_tender_evaluation')}}</span>
                </div>
            </div>
            <div class="card-body card-schedule" style="padding-top: 20px;">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th class="text-right" style="width: 25%">{{__('tender.schedule.fields.start_date', ['type' => __('tender.schedule_type_label.technical')])}}</th>
                        <td class="text-center" style="width: 2%">:</td>
                        <td style="width: 20%">{{$schedule ? $schedule->start_date : ''}} </td>
                        <th class="text-right" style="width: 20%"></th>
                        <td class="text-center" style="width: 2%"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="text-right" style="width: 25%">{{__('tender.schedule.fields.end_date', ['type' => __('tender.schedule_type_label.technical')])}}</th>
                        <td class="text-center" style="width: 2%">:</td>
                        <td style="width: 20%">{{$schedule ? $schedule->end_date : ''}} </td>
                        <th class="text-right" style="width: 20%"></th>
                        <td class="text-center" style="width: 2%"></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="app-footer">
        <div class="app-footer__inner">
            <div class="app-footer-left"></div>
            <div class="app-footer-right">
                @if($statusProcess == "" && $next != $type)
                    <button class="btn btn-primary btn_next_flow">
                        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                @endif
            </div>
        </div>
    </div>

</div>
