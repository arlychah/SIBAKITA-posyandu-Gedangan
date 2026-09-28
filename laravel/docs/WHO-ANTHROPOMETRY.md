# SIBAKITA — perhitungan BB/TB WHO 2006

Dokumen ini mencatat rumus lama dan rumus aktif untuk tinjauan teknis. Ini bukan pengganti penilaian klinis.

## Rumus lama (sudah dinonaktifkan)

Implementasi lama di `app/Helpers/StatusGizi.php` mengaproksimasi median berat memakai polinomial buatan:

```text
Laki-laki: M(h) = 0.0245 × h² − 2.2 × h + 52.0
Perempuan:  M(h) = 0.0240 × h² − 2.15 × h + 51.0
SD = 0.11 × M(h)
z = (berat − M(h)) / SD
```

`h` adalah tinggi dalam sentimeter. Rumus ini **bukan rumus WHO** dan menghasilkan median sekitar 77,5 kg pada tinggi 101 cm, sehingga menandai contoh 15,2 kg sebagai gizi buruk secara keliru. Perhitungan baru tidak memakai polinomial tersebut.

## Acuan dan data aktif

Tabel XLSX WHO Child Growth Standards 2006 berisi kolom `L`, `M`, dan `S` pada interval 0,5 cm. Salinan nilai `M/S` dikompresi di `app/Support/Who2006WflWfhTable.php`; nilai `L` sex-specific dicatat pada header dataset. Data statis menjaga aplikasi dapat menghitung tanpa koneksi jaringan runtime.

- [WHO weight-for-length/height standards](https://www.who.int/tools/child-growth-standards/standards/weight-for-length-height)
- [WFL boys 0–2 years LMS](https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-length-height/wfl_boys_0-to-2-years_zscores.xlsx?sfvrsn=e27a9da3_7)
- [WFL girls 0–2 years LMS](https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-length-height/wfl_girls_0-to-2-years_zscores.xlsx?sfvrsn=288bc4e4_7)
- [WFH boys 2–5 years LMS](https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-length-height/wfh_boys_2-to-5-years_zscores.xlsx?sfvrsn=202c0545_7)
- [WFH girls 2–5 years LMS](https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-length-height/wfh_girls_2-to-5-years_zscores.xlsx?sfvrsn=4d66af6a_7)
- [WHO Anthro manual and measurement notes](https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/software/anthro-pc-manual-v322.pdf)
- [WHO acute malnutrition guideline and thresholds](https://www.who.int/publications/i/item/9789240082830)

## Algoritme aktif

1. Hitung umur dalam bulan pada **tanggal pemeriksaan**.
2. Pilih referensi sex-specific: WFL untuk umur `<24` bulan; WFH untuk umur `≥24` sampai `60` bulan.
3. Terapkan penyesuaian posisi pengukuran jika berbeda dari posisi referensi WHO: tambah 0,7 cm untuk pengukuran berdiri pada referensi panjang berbaring; kurangi 0,7 cm untuk pengukuran berbaring pada referensi tinggi berdiri.
4. Interpolasikan `M` dan `S` secara linear antara dua baris tabel setengah sentimeter di sekitar panjang/tinggi yang sudah disesuaikan. `L` WHO tetap untuk kombinasi indikator/jenis kelamin.
5. Hitung z-score LMS:

```text
z = ((berat / M)^L − 1) / (L × S), untuk L ≠ 0
z = ln(berat / M) / S, untuk L = 0
```

6. Tampilkan evaluasi manual bila usia, berat, posisi ukur, atau panjang/tinggi berada di luar rentang referensi—jangan meng-clamp nilai ke batas tabel.

## Klasifikasi BB/TB

- `z < -3`: Gizi buruk / severely wasted
- `-3 ≤ z < -2`: Gizi kurang / wasted
- `-2 ≤ z ≤ +1`: Gizi normal
- `+1 < z ≤ +2`: berisiko gizi lebih
- `+2 < z ≤ +3`: gizi lebih / overweight
- `z > +3`: obesitas

Pastikan pemetaan istilah lokal Kemenkes disetujui petugas sebelum dipakai sebagai diagnosis klinis.

## Contoh verifikasi resmi

Anak laki-laki sekitar 54 bulan, berat 15,2 kg, tinggi berdiri 101,4 cm:

- WFH boys: pada 101,0 cm `L=-0.3521, M=15.6412, S=0.08277`; pada 101,5 cm `M=15.7857, S=0.08302`.
- Interpolasi 101,4 cm menghasilkan `M≈15.7568, S≈0.08297`.
- Rumus LMS menghasilkan `z≈-0.44`, status BB/TB normal (bukan wasting).

Nilai ini dijadikan assertion regresi di `tests/Unit/StatusGiziTest.php`.
