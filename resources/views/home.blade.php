@extends('layouts.app')
@section('content')
    <h1 class="display-5 text-primary">Welcome to Resto App!</h1>
    <hr class="my-4 text-secondary opacity-50">
    <div class="lead text-dark">
        <p>Lorem ipsum dolor sit amet...</p>
    </div>
    <div>
        <a href="./product" class="btn btn-sm btn-primary">Daftar Produk</a>
    </div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('vendor/vuejs/vue.global.js') }}"></script>
@endsection