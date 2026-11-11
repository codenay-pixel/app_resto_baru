@extends('layouts.app')
@section('content')
<h1 class="display-5 text-primary">List Product</h1>
<a class="btn btn-sm btn-primary" href="./add_product">Add Product</a>
<hr class="my-4 text-secondary opacity-50">

<div v-if="sedangMemuat" class="d-flex justify-content-center my-5">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Memuat data...</span>
    </div>
</div>

<div v-else-if="pesanEror" class="alert alert-danger" role="alert">
    @{{ pesanEror }}
</div>

<div v-else-if="daftarProduk.length === 0" class="alert alert-warning" role="alert">
    Belum ada produk yang tersedia di database.
</div>

<div v-else class="row row-cols-1 row-cols-md-3 g-4">
    <div class="col" v-for="produk in daftarProduk" :key="produk.id">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title fw-bold text-dark">@{{ produk.name }}</h5>
                <p class="card-text text-muted flex-grow-1">@{{ produk.deskripsi }}</p>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <span class="fs-5 fw-bold text-success">Rp @{{ formatRupiah(produk.modal) }}</span>
                    <button class="btn btn-primary btn-sm px-3" @click="lihatDetail(produk)">
                        Detail
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
<!-- LAPISAN MODAL DIALOG (Terletak di luar <main> tepat di bawah body) -->
<div v-if="modalTerpilih" class="position-absolute bg-white shadow-lg border p-4 rounded" 
        style="z-index: 1050; width: 350px; top: 30%; left: 38%;">
    <h5 class="fw-bold">@{{ modalTerpilih.name }}</h5>
    <p class="text-muted">@{{ modalTerpilih.deskripsi }}</p>
    <p><strong>Stok Tersedia:</strong> @{{ modalTerpilih.stok }}</p>
    <button class="btn btn-sm btn-secondary w-100" @click="modalTerpilih = null">Tutup Detail</button>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('vendor/vuejs/vue.global.js') }}"></script>
<script>
    const { createApp, ref, onMounted } = Vue;

    createApp({
        setup() {
            const daftarProduk = ref([]);
            const sedangMemuat = ref(true);
            const pesanEror = ref(null);
            const modalTerpilih = ref(null);

            function ambilDataProduk() {
                sedangMemuat.value = true;
                pesanEror.value = null;

                fetch('/api/data_product', {
                    method: 'GET'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal mengambil data dari server (Status: ' + response.status + ')');
                    }
                    return response.json();
                })
                .then(data => {
                    daftarProduk.value = data;
                    sedangMemuat.value = false;
                })
                .catch(error => {
                    console.error("Eror Fetch:", error);
                    pesanEror.value = error.message;
                    sedangMemuat.value = false;
                });
            }

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            function lihatDetail(produk) {
                modalTerpilih.value = produk;
            }

            onMounted(() => {
                ambilDataProduk();
            });

            return {
                daftarProduk,
                sedangMemuat,
                pesanEror,
                modalTerpilih,
                formatRupiah,
                lihatDetail
            }
        }
    }).mount('#app');
</script>
@endsection