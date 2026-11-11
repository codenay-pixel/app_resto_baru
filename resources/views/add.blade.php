@extends('layouts.app')
@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h2 fw-bold text-dark">Tambah Produk Baru</h1>
    <a href="/product" class="btn btn-outline-secondary btn-sm">Kembali ke Katalog</a>
</div>
<hr class="my-4 text-secondary opacity-50">
<div v-if="notifikasi.pesan" :class="['alert', notifikasi.sukses ? 'alert-success' : 'alert-danger']" role="alert">
    @{{ notifikasi.pesan }}
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">
        <!-- .prevent menghentikan page reload bawaan browser, dialihkan ke fungsi Vue -->
        <form @submit.prevent="submitForm">
            
            <!-- Input 1: Nama Produk -->
            <div class="mb-3">
                <label for="nama_produk" class="form-label fw-semibold">Nama Produk</label>
                <input type="text" id="nama_produk" class="form-control" 
                        v-model.trim="form.name" placeholder="Masukkan nama makanan/minuman" required>
            </div>

            <!-- Input 2: Harga & Stok (Berdampingan dalam Grid) -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="harga" class="form-label fw-semibold">Harga Modal (Rp)</label>
                    <input type="number" id="harga" class="form-control" 
                            v-model.number="form.modal" placeholder="Contoh: 25000" min="0" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="stok" class="form-label fw-semibold">Stok Awal</label>
                    <input type="number" id="stok" class="form-control" 
                            v-model.number="form.stok" placeholder="Contoh: 50" min="0" required>
                </div>
            </div>

            <!-- Input 3: Deskripsi -->
            <div class="mb-4">
                <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                <textarea id="deskripsi" class="form-control" rows="4" 
                            v-model.trim="form.deskripsi" placeholder="Jelaskan detail komposisi atau rasa produk..." required></textarea>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary fw-bold py-2" :disabled="sedangKirim">
                    <span v-if="sedangKirim" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Simpan Produk ke Database
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('vendor/vuejs/vue.global.js') }}"></script>
<script>
    const { createApp, ref } = Vue;

    createApp({
        setup() {
            const sedangKirim = ref(false);
            const notifikasi = ref({ sukses: true, pesan: null });

            const form = ref({
                name: '',
                modal: '',
                stok: '',
                deskripsi: ''
            });

            function resetForm() {
                form.value = {
                    name: '',
                    modal: '',
                    stok: '',
                    deskripsi: ''
                };
            }

            function submitForm() {
                sedangKirim.value = true;
                notifikasi.value.pesan = null;

                fetch('/api/create_product', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(form.value)
                })
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Server gagal memproses data (Status: ' + res.status + ')');
                    }
                    return res.json();
                })
                .then(response => {
                    if (response.status === 'success') {
                        notifikasi.value = { sukses: true, pesan: 'Produk baru berhasil ditambahkan!' };
                        resetForm();
                    } else {
                        notifikasi.value = { sukses: false, pesan: response.message || 'Gagal menyimpan data.' };
                    }
                    sedangKirim.value = false;
                })
                .catch(err => {
                    console.error("Eror Kirim Form:", err);
                    notifikasi.value = { sukses: false, pesan: err.message };
                    sedangKirim.value = false;
                });
            }

            return {
                form,
                sedangKirim,
                notifikasi,
                submitForm
            }
        }
    }).mount('#app');
</script>
@endsection