from flask import Flask, render_template, request, redirect, url_for, flash, abort
from flask_sqlalchemy import SQLAlchemy
from datetime import datetime, date
import os
from math import sqrt

app = Flask(__name__)
app.config['SECRET_KEY'] = 'posyandu-secret-key-2024'
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///posyandu.db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)

@app.context_processor
def inject_globals():
    return dict(datetime=datetime)

class Warga(db.Model):
    __tablename__ = 'warga'
    id = db.Column(db.Integer, primary_key=True)
    nik = db.Column(db.String(16), unique=True, nullable=False)
    nama_lengkap = db.Column(db.String(200), nullable=False)
    whatsapp = db.Column(db.String(15), nullable=False)
    puskesmas = db.Column(db.String(150), nullable=False)
    pustu = db.Column(db.String(150), nullable=False)
    jenis_kelamin = db.Column(db.String(20), nullable=False)
    tempat_lahir = db.Column(db.String(100))
    tanggal_lahir = db.Column(db.Date, nullable=False)
    alamat = db.Column(db.Text)
    kategori = db.Column(db.String(50), nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.now)
    balita = db.relationship('Balita', backref='warga', uselist=False, cascade='all, delete-orphan')
    ibu_hamil = db.relationship('IbuHamil', backref='warga', uselist=False, cascade='all, delete-orphan')
    lansia = db.relationship('Lansia', backref='warga', uselist=False, cascade='all, delete-orphan')

class Balita(db.Model):
    __tablename__ = 'balita'
    id = db.Column(db.Integer, primary_key=True)
    warga_id = db.Column(db.Integer, db.ForeignKey('warga.id'), unique=True, nullable=False)
    nama_ibu = db.Column(db.String(200))
    nama_ayah = db.Column(db.String(200))
    riwayat = db.relationship('PemeriksaanBalita', backref='balita', cascade='all, delete-orphan')

class PemeriksaanBalita(db.Model):
    __tablename__ = 'pemeriksaan_balita'
    id = db.Column(db.Integer, primary_key=True)
    balita_id = db.Column(db.Integer, db.ForeignKey('balita.id'), nullable=False)
    tanggal = db.Column(db.Date, default=date.today, nullable=False)
    berat_badan = db.Column(db.Float, nullable=False)
    tinggi_badan = db.Column(db.Float, nullable=False)
    lingkar_kepala = db.Column(db.Float)
    status_gizi_bbu = db.Column(db.String(50))
    status_gizi_tbu = db.Column(db.String(50))
    status_gizi_bbtb = db.Column(db.String(50))
    asi_eksklusif = db.Column(db.String(20))
    imunisasi_bcg = db.Column(db.Boolean, default=False)
    imunisasi_dpt1 = db.Column(db.Boolean, default=False)
    imunisasi_dpt2 = db.Column(db.Boolean, default=False)
    imunisasi_dpt3 = db.Column(db.Boolean, default=False)
    imunisasi_polio1 = db.Column(db.Boolean, default=False)
    imunisasi_polio2 = db.Column(db.Boolean, default=False)
    imunisasi_polio3 = db.Column(db.Boolean, default=False)
    imunisasi_campak = db.Column(db.Boolean, default=False)
    vitamin_a_bulan_ke = db.Column(db.Integer)
    pmt_diterima = db.Column(db.String(100))
    catatan = db.Column(db.Text)

class IbuHamil(db.Model):
    __tablename__ = 'ibu_hamil'
    id = db.Column(db.Integer, primary_key=True)
    warga_id = db.Column(db.Integer, db.ForeignKey('warga.id'), unique=True, nullable=False)
    nama_suami = db.Column(db.String(200))
    riwayat = db.relationship('PemeriksaanIbuHamil', backref='ibu_hamil', cascade='all, delete-orphan')

class PemeriksaanIbuHamil(db.Model):
    __tablename__ = 'pemeriksaan_ibu_hamil'
    id = db.Column(db.Integer, primary_key=True)
    ibu_hamil_id = db.Column(db.Integer, db.ForeignKey('ibu_hamil.id'), nullable=False)
    tanggal = db.Column(db.Date, default=date.today, nullable=False)
    kehamilan_ke = db.Column(db.Integer)
    usia_kehamilan = db.Column(db.Integer)
    berat_badan = db.Column(db.Float)
    tekanan_darah_sistolik = db.Column(db.Integer)
    tekanan_darah_diastolik = db.Column(db.Integer)
    lila = db.Column(db.Float)
    tinggi_fundus = db.Column(db.Float)
    detak_jantung_janin = db.Column(db.Integer)
    ttd_diberikan = db.Column(db.Boolean, default=False)
    jumlah_ttd = db.Column(db.Integer)
    imunisasi_tt = db.Column(db.String(50))
    catatan = db.Column(db.Text)

class Lansia(db.Model):
    __tablename__ = 'lansia'
    id = db.Column(db.Integer, primary_key=True)
    warga_id = db.Column(db.Integer, db.ForeignKey('warga.id'), unique=True, nullable=False)
    riwayat = db.relationship('PemeriksaanLansia', backref='lansia', cascade='all, delete-orphan')

class PemeriksaanLansia(db.Model):
    __tablename__ = 'pemeriksaan_lansia'
    id = db.Column(db.Integer, primary_key=True)
    lansia_id = db.Column(db.Integer, db.ForeignKey('lansia.id'), nullable=False)
    tanggal = db.Column(db.Date, default=date.today, nullable=False)
    berat_badan = db.Column(db.Float)
    tinggi_badan = db.Column(db.Float)
    tekanan_darah_sistolik = db.Column(db.Integer)
    tekanan_darah_diastolik = db.Column(db.Integer)
    gula_darah_puasa = db.Column(db.Float)
    gula_darah_sewaktu = db.Column(db.Float)
    kolesterol = db.Column(db.Float)
    asam_urat = db.Column(db.Float)
    skrining_jiwa = db.Column(db.String(100))
    penglihatan = db.Column(db.String(100))
    pendengaran = db.Column(db.String(100))
    catatan = db.Column(db.Text)

def hitung_umur(tanggal_lahir):
    today = date.today()
    umur_tahun = today.year - tanggal_lahir.year
    if today.month < tanggal_lahir.month or (today.month == tanggal_lahir.month and today.day < tanggal_lahir.day):
        umur_tahun -= 1
    umur_bulan = umur_tahun * 12 + (today.month - tanggal_lahir.month)
    if today.day < tanggal_lahir.day:
        umur_bulan -= 1
    return umur_tahun, umur_bulan

def klasifikasi_status_gizi_bbu(z_score):
    if z_score < -3:
        return "Berat Badan Sangat Kurang (Severely Underweight)"
    elif z_score < -2:
        return "Berat Badan Kurang (Underweight)"
    elif z_score <= 2:
        return "Berat Badan Normal"
    else:
        return "Risiko Berat Badan Lebih"

def klasifikasi_status_gizi_tbu(z_score):
    if z_score < -3:
        return "Sangat Pendek (Severely Stunted)"
    elif z_score < -2:
        return "Pendek (Stunted)"
    elif z_score <= 3:
        return "Tinggi Normal"
    else:
        return "Tinggi"

def klasifikasi_status_gizi_bbtb(z_score):
    if z_score < -3:
        return "Gizi Buruk (Severely Wasted)"
    elif z_score < -2:
        return "Gizi Kurang (Wasted)"
    elif z_score <= 1:
        return "Gizi Normal"
    elif z_score <= 2:
        return "Beresiko Gizi Lebih"
    elif z_score <= 3:
        return "Gizi Lebih (Overweight)"
    else:
        return "Obesitas"

def hitung_z_score_bbu(berat_badan, umur_bulan, jenis_kelamin):
    median_table = {
        'L': {0: 3.3, 1: 4.5, 2: 5.6, 3: 6.4, 6: 7.9, 9: 8.9, 12: 9.6, 18: 10.9, 24: 12.2, 30: 13.3, 36: 14.3, 42: 15.3, 48: 16.3, 54: 17.1, 60: 18.0},
        'P': {0: 3.2, 1: 4.2, 2: 5.1, 3: 5.8, 6: 7.3, 9: 8.2, 12: 8.9, 18: 10.2, 24: 11.5, 30: 12.7, 36: 13.9, 42: 15.0, 48: 16.0, 54: 16.9, 60: 17.7}
    }
    sd_table = {
        'L': {0: 0.4, 1: 0.5, 2: 0.5, 3: 0.6, 6: 0.7, 9: 0.8, 12: 0.9, 18: 1.0, 24: 1.1, 30: 1.2, 36: 1.3, 42: 1.4, 48: 1.5, 54: 1.6, 60: 1.7},
        'P': {0: 0.4, 1: 0.4, 2: 0.5, 3: 0.5, 6: 0.6, 9: 0.7, 12: 0.8, 18: 0.9, 24: 1.0, 30: 1.1, 36: 1.2, 42: 1.3, 48: 1.4, 54: 1.5, 60: 1.5}
    }
    keys = sorted(median_table[jenis_kelamin].keys())
    lower, upper = 0, 60
    for k in keys:
        if umur_bulan >= k:
            lower = k
        if umur_bulan <= k:
            upper = k
            break
    if lower == upper:
        median = median_table[jenis_kelamin][lower]
        sd = sd_table[jenis_kelamin][lower]
    else:
        ratio = (umur_bulan - lower) / (upper - lower)
        median = median_table[jenis_kelamin][lower] + ratio * (median_table[jenis_kelamin][upper] - median_table[jenis_kelamin][lower])
        sd = sd_table[jenis_kelamin][lower] + ratio * (sd_table[jenis_kelamin][upper] - sd_table[jenis_kelamin][lower])
    return (berat_badan - median) / sd if sd > 0 else 0

def hitung_z_score_tbu(tinggi_badan, umur_bulan, jenis_kelamin):
    median_table = {
        'L': {0: 50.4, 1: 54.7, 2: 58.4, 3: 61.4, 6: 67.6, 9: 72.0, 12: 75.7, 18: 82.3, 24: 87.8, 30: 92.6, 36: 96.1, 42: 100.0, 48: 103.3, 54: 106.7, 60: 110.0},
        'P': {0: 49.8, 1: 53.7, 2: 57.1, 3: 59.8, 6: 65.7, 9: 70.1, 12: 74.0, 18: 80.7, 24: 86.4, 30: 91.2, 36: 95.1, 42: 98.9, 48: 102.7, 54: 106.0, 60: 109.4}
    }
    sd_table = {
        'L': {0: 2.0, 1: 2.1, 2: 2.2, 3: 2.3, 6: 2.5, 9: 2.7, 12: 2.9, 18: 3.2, 24: 3.6, 30: 3.8, 36: 3.9, 42: 4.1, 48: 4.3, 54: 4.4, 60: 4.6},
        'P': {0: 1.9, 1: 2.0, 2: 2.1, 3: 2.2, 6: 2.4, 9: 2.6, 12: 2.8, 18: 3.1, 24: 3.5, 30: 3.7, 36: 3.8, 42: 4.0, 48: 4.2, 54: 4.3, 60: 4.5}
    }
    keys = sorted(median_table[jenis_kelamin].keys())
    lower, upper = 0, 60
    for k in keys:
        if umur_bulan >= k:
            lower = k
        if umur_bulan <= k:
            upper = k
            break
    if lower == upper:
        median = median_table[jenis_kelamin][lower]
        sd = sd_table[jenis_kelamin][lower]
    else:
        ratio = (umur_bulan - lower) / (upper - lower)
        median = median_table[jenis_kelamin][lower] + ratio * (median_table[jenis_kelamin][upper] - median_table[jenis_kelamin][lower])
        sd = sd_table[jenis_kelamin][lower] + ratio * (sd_table[jenis_kelamin][upper] - sd_table[jenis_kelamin][lower])
    return (tinggi_badan - median) / sd if sd > 0 else 0

def hitung_z_score_bbtb(berat_badan, tinggi_badan, jenis_kelamin):
    tb_bulat = int(tinggi_badan)
    if tb_bulat < 49:
        tb_bulat = 49
    if tb_bulat > 120:
        tb_bulat = 120
    median = 0.0245 * tb_bulat * tb_bulat - 2.2 * tb_bulat + 52.0 if jenis_kelamin == 'L' else 0.024 * tb_bulat * tb_bulat - 2.15 * tb_bulat + 51.0
    sd = median * 0.11
    return (berat_badan - median) / sd if sd > 0 else 0

@app.route('/')
def dashboard():
    total_warga = Warga.query.count()
    total_balita = Warga.query.filter_by(kategori='Balita').count()
    total_ibu_hamil = Warga.query.filter_by(kategori='Ibu Hamil').count()
    total_lansia = Warga.query.filter_by(kategori='Lansia').count()
    pemeriksaan_balita_total = PemeriksaanBalita.query.count()
    pemeriksaan_ibu_total = PemeriksaanIbuHamil.query.count()
    pemeriksaan_lansia_total = PemeriksaanLansia.query.count()
    warga_terbaru = Warga.query.order_by(Warga.created_at.desc()).limit(10).all()
    for w in warga_terbaru:
        w.umur_tahun, w.umur_bulan = hitung_umur(w.tanggal_lahir)
    return render_template('dashboard.html',
                         total_warga=total_warga,
                         total_balita=total_balita,
                         total_ibu_hamil=total_ibu_hamil,
                         total_lansia=total_lansia,
                         pemeriksaan_balita_total=pemeriksaan_balita_total,
                         pemeriksaan_ibu_total=pemeriksaan_ibu_total,
                         pemeriksaan_lansia_total=pemeriksaan_lansia_total,
                         warga_terbaru=warga_terbaru)

@app.route('/warga')
def daftar_warga():
    kategori = request.args.get('kategori', '')
    search = request.args.get('search', '')
    query = Warga.query
    if kategori:
        query = query.filter_by(kategori=kategori)
    if search:
        query = query.filter((Warga.nama_lengkap.contains(search)) | (Warga.nik.contains(search)))
    warga_list = query.order_by(Warga.created_at.desc()).all()
    for w in warga_list:
        w.umur_tahun, w.umur_bulan = hitung_umur(w.tanggal_lahir)
    return render_template('warga/index.html', warga_list=warga_list, kategori=kategori, search=search)

@app.route('/warga/tambah', methods=['GET', 'POST'])
def tambah_warga():
    if request.method == 'POST':
        nik = request.form['nik'].strip()
        if len(nik) != 16 or not nik.isdigit():
            flash('NIK harus 16 digit angka!', 'danger')
            return redirect(url_for('tambah_warga'))
        if Warga.query.filter_by(nik=nik).first():
            flash('NIK sudah terdaftar!', 'warning')
            return redirect(url_for('tambah_warga'))
        try:
            tanggal_lahir = datetime.strptime(request.form['tanggal_lahir'], '%Y-%m-%d').date()
        except ValueError:
            flash('Tanggal lahir tidak valid!', 'danger')
            return redirect(url_for('tambah_warga'))
        warga = Warga(
            nik=nik,
            nama_lengkap=request.form['nama_lengkap'].strip(),
            whatsapp=request.form['whatsapp'].strip(),
            puskesmas=request.form['puskesmas'].strip(),
            pustu=request.form['pustu'].strip(),
            jenis_kelamin=request.form['jenis_kelamin'],
            tempat_lahir=request.form.get('tempat_lahir', '').strip(),
            tanggal_lahir=tanggal_lahir,
            alamat=request.form.get('alamat', '').strip(),
            kategori=request.form['kategori']
        )
        db.session.add(warga)
        db.session.flush()
        if request.form['kategori'] == 'Balita':
            balita = Balita(warga_id=warga.id, nama_ibu=request.form.get('nama_ibu', ''), nama_ayah=request.form.get('nama_ayah', ''))
            db.session.add(balita)
        elif request.form['kategori'] == 'Ibu Hamil':
            ibu = IbuHamil(warga_id=warga.id, nama_suami=request.form.get('nama_suami', ''))
            db.session.add(ibu)
        elif request.form['kategori'] == 'Lansia':
            lansia = Lansia(warga_id=warga.id)
            db.session.add(lansia)
        db.session.commit()
        flash('Data warga berhasil ditambahkan!', 'success')
        return redirect(url_for('detail_warga', id=warga.id))
    return render_template('warga/tambah.html')

@app.route('/warga/<int:id>')
def detail_warga(id):
    warga = Warga.query.get_or_404(id)
    warga.umur_tahun, warga.umur_bulan = hitung_umur(warga.tanggal_lahir)
    return render_template('warga/detail.html', warga=warga)

@app.route('/warga/<int:id>/edit', methods=['GET', 'POST'])
def edit_warga(id):
    warga = Warga.query.get_or_404(id)
    if request.method == 'POST':
        try:
            tanggal_lahir = datetime.strptime(request.form['tanggal_lahir'], '%Y-%m-%d').date()
        except ValueError:
            flash('Tanggal lahir tidak valid!', 'danger')
            return redirect(url_for('edit_warga', id=id))
        warga.nik = request.form['nik'].strip()
        warga.nama_lengkap = request.form['nama_lengkap'].strip()
        warga.whatsapp = request.form['whatsapp'].strip()
        warga.puskesmas = request.form['puskesmas'].strip()
        warga.pustu = request.form['pustu'].strip()
        warga.jenis_kelamin = request.form['jenis_kelamin']
        warga.tempat_lahir = request.form.get('tempat_lahir', '').strip()
        warga.tanggal_lahir = tanggal_lahir
        warga.alamat = request.form.get('alamat', '').strip()
        if request.form['kategori'] != warga.kategori:
            if warga.kategori == 'Balita' and warga.balita:
                db.session.delete(warga.balita)
            elif warga.kategori == 'Ibu Hamil' and warga.ibu_hamil:
                db.session.delete(warga.ibu_hamil)
            elif warga.kategori == 'Lansia' and warga.lansia:
                db.session.delete(warga.lansia)
            warga.kategori = request.form['kategori']
            if warga.kategori == 'Balita' and not warga.balita:
                db.session.add(Balita(warga_id=warga.id, nama_ibu=request.form.get('nama_ibu', ''), nama_ayah=request.form.get('nama_ayah', '')))
            elif warga.kategori == 'Ibu Hamil' and not warga.ibu_hamil:
                db.session.add(IbuHamil(warga_id=warga.id, nama_suami=request.form.get('nama_suami', '')))
            elif warga.kategori == 'Lansia' and not warga.lansia:
                db.session.add(Lansia(warga_id=warga.id))
        else:
            if warga.kategori == 'Balita' and warga.balita:
                warga.balita.nama_ibu = request.form.get('nama_ibu', '')
                warga.balita.nama_ayah = request.form.get('nama_ayah', '')
            elif warga.kategori == 'Ibu Hamil' and warga.ibu_hamil:
                warga.ibu_hamil.nama_suami = request.form.get('nama_suami', '')
        db.session.commit()
        flash('Data warga berhasil diperbarui!', 'success')
        return redirect(url_for('detail_warga', id=id))
    return render_template('warga/edit.html', warga=warga)

@app.route('/warga/<int:id>/hapus', methods=['POST'])
def hapus_warga(id):
    warga = Warga.query.get_or_404(id)
    db.session.delete(warga)
    db.session.commit()
    flash('Data warga berhasil dihapus!', 'success')
    return redirect(url_for('daftar_warga'))

@app.route('/balita/<int:id>/periksa', methods=['GET', 'POST'])
def periksa_balita(id):
    balita = Balita.query.get_or_404(id)
    umur_tahun, umur_bulan = hitung_umur(balita.warga.tanggal_lahir)
    if request.method == 'POST':
        try:
            tanggal = datetime.strptime(request.form['tanggal'], '%Y-%m-%d').date()
            berat_badan = float(request.form['berat_badan'])
            tinggi_badan = float(request.form['tinggi_badan'])
            lingkar_kepala = request.form.get('lingkar_kepala')
            lingkar_kepala = float(lingkar_kepala) if lingkar_kepala else None
        except ValueError:
            flash('Data pengukuran tidak valid!', 'danger')
            return redirect(url_for('periksa_balita', id=id))
        jk = 'L' if balita.warga.jenis_kelamin == 'Laki-laki' else 'P'
        z_bbu = hitung_z_score_bbu(berat_badan, umur_bulan, jk)
        z_tbu = hitung_z_score_tbu(tinggi_badan, umur_bulan, jk)
        z_bbtb = hitung_z_score_bbtb(berat_badan, tinggi_badan, jk)
        pemeriksaan = PemeriksaanBalita(
            balita_id=id,
            tanggal=tanggal,
            berat_badan=berat_badan,
            tinggi_badan=tinggi_badan,
            lingkar_kepala=lingkar_kepala,
            status_gizi_bbu=klasifikasi_status_gizi_bbu(z_bbu),
            status_gizi_tbu=klasifikasi_status_gizi_tbu(z_tbu),
            status_gizi_bbtb=klasifikasi_status_gizi_bbtb(z_bbtb),
            asi_eksklusif=request.form.get('asi_eksklusif', ''),
            imunisasi_bcg=request.form.get('imunisasi_bcg') == 'on',
            imunisasi_dpt1=request.form.get('imunisasi_dpt1') == 'on',
            imunisasi_dpt2=request.form.get('imunisasi_dpt2') == 'on',
            imunisasi_dpt3=request.form.get('imunisasi_dpt3') == 'on',
            imunisasi_polio1=request.form.get('imunisasi_polio1') == 'on',
            imunisasi_polio2=request.form.get('imunisasi_polio2') == 'on',
            imunisasi_polio3=request.form.get('imunisasi_polio3') == 'on',
            imunisasi_campak=request.form.get('imunisasi_campak') == 'on',
            vitamin_a_bulan_ke=int(request.form['vitamin_a_bulan_ke']) if request.form.get('vitamin_a_bulan_ke') else None,
            pmt_diterima=request.form.get('pmt_diterima', ''),
            catatan=request.form.get('catatan', '')
        )
        db.session.add(pemeriksaan)
        db.session.commit()
        flash('Pemeriksaan balita berhasil dicatat! Status gizi: ' + klasifikasi_status_gizi_bbtb(z_bbtb), 'success')
        return redirect(url_for('detail_warga', id=balita.warga.id))
    riwayat = PemeriksaanBalita.query.filter_by(balita_id=id).order_by(PemeriksaanBalita.tanggal.desc()).all()
    return render_template('balita/periksa.html', balita=balita, umur_tahun=umur_tahun, umur_bulan=umur_bulan, riwayat=riwayat)

@app.route('/ibu-hamil/<int:id>/periksa', methods=['GET', 'POST'])
def periksa_ibu_hamil(id):
    ibu = IbuHamil.query.get_or_404(id)
    if request.method == 'POST':
        try:
            tanggal = datetime.strptime(request.form['tanggal'], '%Y-%m-%d').date()
        except ValueError:
            flash('Tanggal tidak valid!', 'danger')
            return redirect(url_for('periksa_ibu_hamil', id=id))
        pemeriksaan = PemeriksaanIbuHamil(
            ibu_hamil_id=id,
            tanggal=tanggal,
            kehamilan_ke=int(request.form['kehamilan_ke']) if request.form.get('kehamilan_ke') else None,
            usia_kehamilan=int(request.form['usia_kehamilan']) if request.form.get('usia_kehamilan') else None,
            berat_badan=float(request.form['berat_badan']) if request.form.get('berat_badan') else None,
            tekanan_darah_sistolik=int(request.form['tekanan_darah_sistolik']) if request.form.get('tekanan_darah_sistolik') else None,
            tekanan_darah_diastolik=int(request.form['tekanan_darah_diastolik']) if request.form.get('tekanan_darah_diastolik') else None,
            lila=float(request.form['lila']) if request.form.get('lila') else None,
            tinggi_fundus=float(request.form['tinggi_fundus']) if request.form.get('tinggi_fundus') else None,
            detak_jantung_janin=int(request.form['detak_jantung_janin']) if request.form.get('detak_jantung_janin') else None,
            ttd_diberikan=request.form.get('ttd_diberikan') == 'on',
            jumlah_ttd=int(request.form['jumlah_ttd']) if request.form.get('jumlah_ttd') else None,
            imunisasi_tt=request.form.get('imunisasi_tt', ''),
            catatan=request.form.get('catatan', '')
        )
        db.session.add(pemeriksaan)
        db.session.commit()
        flash('Pemeriksaan ibu hamil berhasil dicatat!', 'success')
        return redirect(url_for('detail_warga', id=ibu.warga.id))
    riwayat = PemeriksaanIbuHamil.query.filter_by(ibu_hamil_id=id).order_by(PemeriksaanIbuHamil.tanggal.desc()).all()
    return render_template('ibu_hamil/periksa.html', ibu=ibu, riwayat=riwayat)

@app.route('/lansia/<int:id>/periksa', methods=['GET', 'POST'])
def periksa_lansia(id):
    lansia = Lansia.query.get_or_404(id)
    if request.method == 'POST':
        try:
            tanggal = datetime.strptime(request.form['tanggal'], '%Y-%m-%d').date()
        except ValueError:
            flash('Tanggal tidak valid!', 'danger')
            return redirect(url_for('periksa_lansia', id=id))
        pemeriksaan = PemeriksaanLansia(
            lansia_id=id,
            tanggal=tanggal,
            berat_badan=float(request.form['berat_badan']) if request.form.get('berat_badan') else None,
            tinggi_badan=float(request.form['tinggi_badan']) if request.form.get('tinggi_badan') else None,
            tekanan_darah_sistolik=int(request.form['tekanan_darah_sistolik']) if request.form.get('tekanan_darah_sistolik') else None,
            tekanan_darah_diastolik=int(request.form['tekanan_darah_diastolik']) if request.form.get('tekanan_darah_diastolik') else None,
            gula_darah_puasa=float(request.form['gula_darah_puasa']) if request.form.get('gula_darah_puasa') else None,
            gula_darah_sewaktu=float(request.form['gula_darah_sewaktu']) if request.form.get('gula_darah_sewaktu') else None,
            kolesterol=float(request.form['kolesterol']) if request.form.get('kolesterol') else None,
            asam_urat=float(request.form['asam_urat']) if request.form.get('asam_urat') else None,
            skrining_jiwa=request.form.get('skrining_jiwa', ''),
            penglihatan=request.form.get('penglihatan', ''),
            pendengaran=request.form.get('pendengaran', ''),
            catatan=request.form.get('catatan', '')
        )
        db.session.add(pemeriksaan)
        db.session.commit()
        flash('Pemeriksaan lansia berhasil dicatat!', 'success')
        return redirect(url_for('detail_warga', id=lansia.warga.id))
    riwayat = PemeriksaanLansia.query.filter_by(lansia_id=id).order_by(PemeriksaanLansia.tanggal.desc()).all()
    return render_template('lansia/periksa.html', lansia=lansia, riwayat=riwayat)

@app.route('/laporan')
def laporan():
    return render_template('laporan.html')

@app.route('/laporan/balita')
def laporan_balita():
    start = request.args.get('start')
    end = request.args.get('end')
    query = PemeriksaanBalita.query
    if start:
        try:
            query = query.filter(PemeriksaanBalita.tanggal >= datetime.strptime(start, '%Y-%m-%d').date())
        except:
            pass
    if end:
        try:
            query = query.filter(PemeriksaanBalita.tanggal <= datetime.strptime(end, '%Y-%m-%d').date())
        except:
            pass
    data = query.order_by(PemeriksaanBalita.tanggal.desc()).all()
    total = len(data)
    stunted = sum(1 for d in data if 'Pendek' in (d.status_gizi_tbu or ''))
    underweight = sum(1 for d in data if 'Kurang' in (d.status_gizi_bbu or ''))
    wasted = sum(1 for d in data if 'Kurang' in (d.status_gizi_bbtb or '') or 'Buruk' in (d.status_gizi_bbtb or ''))
    return render_template('laporan/balita.html', data=data, total=total, stunted=stunted, underweight=underweight, wasted=wasted, start=start, end=end)

@app.route('/laporan/ibu-hamil')
def laporan_ibu_hamil():
    start = request.args.get('start')
    end = request.args.get('end')
    query = PemeriksaanIbuHamil.query
    if start:
        try:
            query = query.filter(PemeriksaanIbuHamil.tanggal >= datetime.strptime(start, '%Y-%m-%d').date())
        except:
            pass
    if end:
        try:
            query = query.filter(PemeriksaanIbuHamil.tanggal <= datetime.strptime(end, '%Y-%m-%d').date())
        except:
            pass
    data = query.order_by(PemeriksaanIbuHamil.tanggal.desc()).all()
    total = len(data)
    risiko_kek = sum(1 for d in data if d.lila and d.lila < 23.5)
    td_tinggi = sum(1 for d in data if d.tekanan_darah_sistolik and d.tekanan_darah_sistolik >= 140)
    ttd_diberikan = sum(1 for d in data if d.ttd_diberikan)
    return render_template('laporan/ibu_hamil.html', data=data, total=total, risiko_kek=risiko_kek, td_tinggi=td_tinggi, ttd_diberikan=ttd_diberikan, start=start, end=end)

@app.route('/laporan/lansia')
def laporan_lansia():
    start = request.args.get('start')
    end = request.args.get('end')
    query = PemeriksaanLansia.query
    if start:
        try:
            query = query.filter(PemeriksaanLansia.tanggal >= datetime.strptime(start, '%Y-%m-%d').date())
        except:
            pass
    if end:
        try:
            query = query.filter(PemeriksaanLansia.tanggal <= datetime.strptime(end, '%Y-%m-%d').date())
        except:
            pass
    data = query.order_by(PemeriksaanLansia.tanggal.desc()).all()
    total = len(data)
    hipertensi = sum(1 for d in data if d.tekanan_darah_sistolik and d.tekanan_darah_sistolik >= 140)
    diabetes = sum(1 for d in data if (d.gula_darah_puasa and d.gula_darah_puasa >= 126) or (d.gula_darah_sewaktu and d.gula_darah_sewaktu >= 200))
    return render_template('laporan/lansia.html', data=data, total=total, hipertensi=hipertensi, diabetes=diabetes, start=start, end=end)

with app.app_context():
    db.create_all()

if __name__ == '__main__':
    app.run(debug=True, port=5000)
