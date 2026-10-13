@php
    $fieldPrefix = $prefix;
@endphp

<div class="row pic-form-fields d-none" data-prefix="{{ $fieldPrefix }}">
<div class="col-12">
    <h5 class="mt-2 mb-3">{{ $title }}</h5>
</div>
<div class="col-md-6 form-group">
    <label>NIK</label>
    <input class="form-control" name="{{ $fieldPrefix }}_national_id_number" id="{{ $fieldPrefix }}_national_id_number" maxlength="16">
    <small class="text-danger error-{{ $fieldPrefix }}_national_id_number"></small>
</div>
<div class="col-md-6 form-group">
    <label>Nama</label>
    <input class="form-control text-uppercase" name="{{ $fieldPrefix }}" id="{{ $fieldPrefix }}">
    <small class="text-danger error-{{ $fieldPrefix }}"></small>
</div>
<div class="col-md-6 form-group">
    <label>Email</label>
    <input class="form-control" name="{{ $fieldPrefix }}_email" id="{{ $fieldPrefix }}_email" type="email">
    <small class="text-danger error-{{ $fieldPrefix }}_email"></small>
</div>
<div class="col-md-6 form-group">
    <label>Password</label>
    <div class="input-group">
        <input class="form-control" name="{{ $fieldPrefix }}_password" id="{{ $fieldPrefix }}_password" type="password">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" onclick="togglePicPassword('{{ $fieldPrefix }}_password', this)">
                <i class="mdi mdi-eye"></i>
            </button>
        </div>
    </div>
    <small class="text-danger error-{{ $fieldPrefix }}_password"></small>
</div>
<div class="col-md-6 form-group">
    <label>Tempat Lahir</label>
    <input class="form-control text-uppercase" name="{{ $fieldPrefix }}_birth_place" id="{{ $fieldPrefix }}_birth_place">
    <small class="text-danger error-{{ $fieldPrefix }}_birth_place"></small>
</div>
<div class="col-md-6 form-group">
    <label>Tanggal Lahir</label>
    <input class="form-control" name="{{ $fieldPrefix }}_birth_date" id="{{ $fieldPrefix }}_birth_date" type="date">
    <small class="text-danger error-{{ $fieldPrefix }}_birth_date"></small>
</div>
<div class="col-md-6 form-group">
    <label>Gender</label>
    <select class="form-control" name="{{ $fieldPrefix }}_gender" id="{{ $fieldPrefix }}_gender">
        <option value="">Pilih</option>
        <option value="male">Laki-laki</option>
        <option value="female">Perempuan</option>
    </select>
    <small class="text-danger error-{{ $fieldPrefix }}_gender"></small>
</div>
<div class="col-md-6 form-group">
    <label>Gol. Darah</label>
    <select class="form-control" name="{{ $fieldPrefix }}_blood_type" id="{{ $fieldPrefix }}_blood_type">
        <option value="">Pilih</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="AB">AB</option>
        <option value="O">O</option>
        <option value="-">-</option>
    </select>
    <small class="text-danger error-{{ $fieldPrefix }}_blood_type"></small>
</div>
<div class="col-md-6 form-group">
    <label>Provinsi</label>
    <select class="form-control" name="{{ $fieldPrefix }}_province_id" id="{{ $fieldPrefix }}_province_id">
        <option value="">Pilih Provinsi</option>
        @foreach ($provinces as $province)
            <option value="{{ $province->id }}">{{ $province->provinsi }}</option>
        @endforeach
    </select>
    <small class="text-danger error-{{ $fieldPrefix }}_province_id"></small>
</div>
<div class="col-md-6 form-group">
    <label>Kota/Kabupaten</label>
    <select class="form-control" name="{{ $fieldPrefix }}_city_id" id="{{ $fieldPrefix }}_city_id"></select>
    <small class="text-danger error-{{ $fieldPrefix }}_city_id"></small>
</div>
<div class="col-md-6 form-group">
    <label>Kecamatan</label>
    <select class="form-control" name="{{ $fieldPrefix }}_district_id" id="{{ $fieldPrefix }}_district_id"></select>
    <small class="text-danger error-{{ $fieldPrefix }}_district_id"></small>
</div>
<div class="col-md-6 form-group">
    <label>Kelurahan/Desa</label>
    <select class="form-control" name="{{ $fieldPrefix }}_village_id" id="{{ $fieldPrefix }}_village_id"></select>
    <small class="text-danger error-{{ $fieldPrefix }}_village_id"></small>
</div>
<div class="col-md-6 form-group">
    <label>RT</label>
    <input class="form-control" name="{{ $fieldPrefix }}_rt" id="{{ $fieldPrefix }}_rt" maxlength="3">
    <small class="text-danger error-{{ $fieldPrefix }}_rt"></small>
</div>
<div class="col-md-6 form-group">
    <label>RW</label>
    <input class="form-control" name="{{ $fieldPrefix }}_rw" id="{{ $fieldPrefix }}_rw" maxlength="3">
    <small class="text-danger error-{{ $fieldPrefix }}_rw"></small>
</div>
<div class="col-md-6 form-group">
    <label>Alamat</label>
    <textarea class="form-control text-uppercase" name="{{ $fieldPrefix }}_address" id="{{ $fieldPrefix }}_address" rows="2"></textarea>
    <small class="text-danger error-{{ $fieldPrefix }}_address"></small>
</div>
<div class="col-md-6 form-group">
    <label>Kode Pos</label>
    <input class="form-control" name="{{ $fieldPrefix }}_postal_code" id="{{ $fieldPrefix }}_postal_code">
    <small class="text-danger error-{{ $fieldPrefix }}_postal_code"></small>
</div>
<div class="col-md-6 form-group">
    <label>Agama</label>
    <select class="form-control" name="{{ $fieldPrefix }}_religion" id="{{ $fieldPrefix }}_religion">
        <option value="">Pilih Agama</option>
        <option value="islam">Islam</option>
        <option value="christian">Kristen</option>
        <option value="catholic">Katolik</option>
        <option value="hindu">Hindu</option>
        <option value="buddhist">Buddha</option>
        <option value="confucian">Konghucu</option>
    </select>
    <small class="text-danger error-{{ $fieldPrefix }}_religion"></small>
</div>
<div class="col-md-6 form-group">
    <label>Status Perkawinan</label>
    <select class="form-control" name="{{ $fieldPrefix }}_marital_status" id="{{ $fieldPrefix }}_marital_status">
        <option value="">Pilih Status Perkawinan</option>
        <option value="single">Belum Kawin</option>
        <option value="married">Kawin</option>
        <option value="divorced">Cerai Hidup</option>
        <option value="widowed">Cerai Mati</option>
    </select>
    <small class="text-danger error-{{ $fieldPrefix }}_marital_status"></small>
</div>
<div class="col-md-6 form-group">
    <label>Pekerjaan</label>
    <input class="form-control text-uppercase" name="{{ $fieldPrefix }}_occupation" id="{{ $fieldPrefix }}_occupation">
    <small class="text-danger error-{{ $fieldPrefix }}_occupation"></small>
</div>
<div class="col-md-6 form-group">
    <label>Kewarganegaraan</label>
    <input class="form-control text-uppercase" name="{{ $fieldPrefix }}_nationality" id="{{ $fieldPrefix }}_nationality" value="WNI">
    <small class="text-danger error-{{ $fieldPrefix }}_nationality"></small>
</div>
<div class="col-md-6 form-group">
    <label>Telepon/WhatsApp</label>
    <input class="form-control" name="{{ $fieldPrefix }}_phone_number" id="{{ $fieldPrefix }}_phone_number">
    <small class="text-danger error-{{ $fieldPrefix }}_phone_number"></small>
</div>
</div>
