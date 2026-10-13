@php
    $fieldPrefix = $prefix;
@endphp

<div class="row lender-pic-form-fields d-none" data-prefix="{{ $fieldPrefix }}">
    <div class="col-12">
        <h5 class="mt-2 mb-3">{{ $title }}</h5>
    </div>
    <div class="col-md-6 form-group">
        <label>NIK</label>
        <input class="form-control" id="{{ $fieldPrefix }}_national_id_number" maxlength="16">
        <small class="text-danger" id="err_{{ $fieldPrefix }}_national_id_number"></small>
    </div>
    <div class="col-md-6 form-group">
        <label>Nama</label>
        <input class="form-control text-uppercase" id="{{ $fieldPrefix }}_name">
        <small class="text-danger" id="err_{{ $fieldPrefix }}_name"></small>
    </div>
    <div class="col-md-6 form-group">
        <label>Email</label>
        <input class="form-control" id="{{ $fieldPrefix }}_email" type="email">
        <small class="text-danger" id="err_{{ $fieldPrefix }}_email"></small>
    </div>
    <div class="col-md-6 form-group">
        <label>Password</label>
        <div class="input-group">
            <input class="form-control" id="{{ $fieldPrefix }}_password" type="password">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" onclick="toggleLenderPicPassword('{{ $fieldPrefix }}_password', this)">
                    <i class="mdi mdi-eye"></i>
                </button>
            </div>
        </div>
        <small class="text-danger" id="err_{{ $fieldPrefix }}_password"></small>
    </div>
    <div class="col-md-6 form-group">
        <label>Tempat Lahir</label>
        <input class="form-control text-uppercase" id="{{ $fieldPrefix }}_birth_place">
    </div>
    <div class="col-md-6 form-group">
        <label>Tanggal Lahir</label>
        <input class="form-control" id="{{ $fieldPrefix }}_birth_date" type="date">
    </div>
    <div class="col-md-6 form-group">
        <label>Gender</label>
        <select class="form-control" id="{{ $fieldPrefix }}_gender">
            <option value="">Pilih</option>
            <option value="male">Laki-laki</option>
            <option value="female">Perempuan</option>
        </select>
    </div>
    <div class="col-md-6 form-group">
        <label>Gol. Darah</label>
        <select class="form-control" id="{{ $fieldPrefix }}_blood_type">
            <option value="">Pilih</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="AB">AB</option>
            <option value="O">O</option>
            <option value="-">-</option>
        </select>
    </div>
    <div class="col-md-6 form-group">
        <label>Provinsi</label>
        <select class="form-control lender-pic-province-select" id="{{ $fieldPrefix }}_province_id" data-prefix="{{ $fieldPrefix }}">
            <option value="">Pilih Provinsi</option>
            @foreach ($data['provinsis'] as $province)
                <option value="{{ $province->id }}">{{ $province->provinsi }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 form-group">
        <label>Kota/Kabupaten</label>
        <select class="form-control lender-pic-city-select" id="{{ $fieldPrefix }}_city_id" data-prefix="{{ $fieldPrefix }}"></select>
    </div>
    <div class="col-md-6 form-group">
        <label>Kecamatan</label>
        <select class="form-control lender-pic-district-select" id="{{ $fieldPrefix }}_district_id" data-prefix="{{ $fieldPrefix }}"></select>
    </div>
    <div class="col-md-6 form-group">
        <label>Kelurahan/Desa</label>
        <select class="form-control lender-pic-village-select" id="{{ $fieldPrefix }}_village_id" data-prefix="{{ $fieldPrefix }}"></select>
    </div>
    <div class="col-md-6 form-group">
        <label>RT</label>
        <input class="form-control" id="{{ $fieldPrefix }}_rt" maxlength="3">
    </div>
    <div class="col-md-6 form-group">
        <label>RW</label>
        <input class="form-control" id="{{ $fieldPrefix }}_rw" maxlength="3">
    </div>
    <div class="col-md-6 form-group">
        <label>Alamat</label>
        <textarea class="form-control text-uppercase" id="{{ $fieldPrefix }}_address" rows="2"></textarea>
    </div>
    <div class="col-md-6 form-group">
        <label>Kode Pos</label>
        <input class="form-control" id="{{ $fieldPrefix }}_postal_code">
    </div>
    <div class="col-md-6 form-group">
        <label>Agama</label>
        <select class="form-control" id="{{ $fieldPrefix }}_religion">
            <option value="">Pilih Agama</option>
            <option value="islam">Islam</option>
            <option value="christian">Kristen</option>
            <option value="catholic">Katolik</option>
            <option value="hindu">Hindu</option>
            <option value="buddhist">Buddha</option>
            <option value="confucian">Konghucu</option>
        </select>
    </div>
    <div class="col-md-6 form-group">
        <label>Status Perkawinan</label>
        <select class="form-control" id="{{ $fieldPrefix }}_marital_status">
            <option value="">Pilih Status Perkawinan</option>
            <option value="single">Belum Kawin</option>
            <option value="married">Kawin</option>
            <option value="divorced">Cerai Hidup</option>
            <option value="widowed">Cerai Mati</option>
        </select>
    </div>
    <div class="col-md-6 form-group">
        <label>Pekerjaan</label>
        <input class="form-control text-uppercase" id="{{ $fieldPrefix }}_occupation">
    </div>
    <div class="col-md-6 form-group">
        <label>Kewarganegaraan</label>
        <input class="form-control text-uppercase" id="{{ $fieldPrefix }}_nationality" value="WNI">
    </div>
    <div class="col-md-6 form-group">
        <label>Telepon/WhatsApp</label>
        <input class="form-control" id="{{ $fieldPrefix }}_phone_number">
    </div>
</div>
