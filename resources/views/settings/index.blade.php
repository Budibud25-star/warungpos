@extends('layouts.app')
@section('title', 'Pengaturan')
@section('content')
<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card p-3">
                <h6>Profil Warung</h6>
                @php $store = $settings->get('store', collect()); @endphp
                @foreach(['store_name'=>'Nama Warung','store_address'=>'Alamat','store_phone'=>'Telepon','store_whatsapp'=>'WhatsApp','store_email'=>'Email','store_website'=>'Website','store_owner'=>'Nama Pemilik'] as $key=>$label)
                    @php $s = $store->firstWhere('key', $key); @endphp
                    <div class="mb-2">
                        <label>{{ $label }}</label>
                        <input type="text" name="{{ $key }}" class="form-control form-control-sm" value="{{ $s->value ?? '' }}">
                    </div>
                @endforeach
                <div class="mb-2">
                    <label>Logo Warung</label>
                    <input type="file" name="store_logo" class="form-control form-control-sm" accept="image/*">
                    @php $logo = $store->firstWhere('key', 'store_logo'); @endphp
                    @if($logo && $logo->value)<img src="{{ asset('storage/'.$logo->value) }}" class="mt-2" style="max-height:80px">@endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3">
                <h6>Pengaturan Struk</h6>
                @php $receipt = $settings->get('receipt', collect()); @endphp
                @foreach(['receipt_show_logo'=>'Tampilkan Logo','receipt_show_address'=>'Tampilkan Alamat','receipt_show_phone'=>'Tampilkan Telepon','receipt_show_cashier'=>'Tampilkan Kasir','receipt_show_customer'=>'Tampilkan Pelanggan','receipt_show_discount'=>'Tampilkan Diskon','receipt_show_tax'=>'Tampilkan Pajak'] as $key=>$label)
                    @php $s = $receipt->firstWhere('key', $key); @endphp
                    <div class="form-check mb-1">
                        <input type="checkbox" name="{{ $key }}" value="1" class="form-check-input" @checked(($s->value ?? '1') == '1')>
                        <label class="form-check-label">{{ $label }}</label>
                    </div>
                @endforeach
                @php $width = $receipt->firstWhere('key', 'receipt_width'); @endphp
                <div class="mb-2 mt-2"><label>Ukuran Struk (mm)</label><input type="number" name="receipt_width" class="form-control form-control-sm" value="{{ $width->value ?? 80 }}"></div>
                @php $footer = $receipt->firstWhere('key', 'receipt_footer'); @endphp
                <div class="mb-2"><label>Pesan Footer</label><textarea name="receipt_footer" class="form-control form-control-sm">{{ $footer->value ?? '' }}</textarea></div>
            </div>

            <div class="card p-3 mt-3">
                <h6>Pengaturan Pajak</h6>
                @php $taxE = $settings->get('payment', collect())->firstWhere('key', 'tax_enabled'); @endphp
                @php $taxP = $settings->get('payment', collect())->firstWhere('key', 'tax_percentage'); @endphp
                <div class="form-check mb-2">
                    <input type="checkbox" name="tax_enabled" value="1" class="form-check-input" @checked(($taxE->value ?? '0') == '1')>
                    <label class="form-check-label">Aktifkan Pajak</label>
                </div>
                <div class="mb-2"><label>Persentase Pajak (%)</label><input type="number" name="tax_percentage" class="form-control form-control-sm" value="{{ $taxP->value ?? 10 }}"></div>
            </div>
        </div>
    </div>
    <button class="btn btn-primary mt-3">Simpan Pengaturan</button>
</form>
@endsection