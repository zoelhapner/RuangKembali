@extends('layouts.website')
@section('content')
<style>
.hero{
    position:relative;
    margin-top:110px;
    height:calc(100vh - 110px);
}
.hero-caption{

    position:absolute;

    left:12%;

    bottom:22%;

    z-index:3;

    color:#fff;

    text-align:left;

    display:block;

    padding:0;

}

.hero-caption h3{

    margin:0;

    font-family: "Montserrat", serif;
    /* atau Playfair Display jika belum memakai Cormorant */

    font-size:38px;

    font-weight:400;

    line-height:1.05;

    letter-spacing:-0.5px;

    color:#fff;

    max-width:720px;

}
.carousel,
.carousel-inner,
.carousel-item{
    height:100%;
}

.carousel-item img{
    width:100%;
    height:100%;
    object-fit:cover;
    object-position:center 20px;
}
.carousel-control-prev,
.carousel-control-next{

    width:90px;

    z-index:4;

}
.carousel-indicators{

    bottom:40px;

    z-index:4;

}

.carousel-indicators button{

    width:120px !important;

    height:4px !important;

    border-radius:20px;

    margin:0 8px !important;
}
@media (max-width: 992px) {
    .hero-content {
        gap: 40px;
    }
    .brand-logo {
        width: 130px;
    }
    .btn-hero {
        min-width: 180px;
    }
}

@media (min-width: 576px) {
    .hero {
        background:
            linear-gradient(
                to bottom,
                rgba(0,0,0,0.65),
                rgba(0,0,0,0.35)
            ),
            url('{{ asset("hero-mobile.jpeg") }}');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: 85% top;
        margin-top:150px;
        height:calc(100vh - 150px);
    }
    .hero-caption{
        left:24px;
        right:24px;
        bottom:90px;
    }
    .hero-caption h3{
        font-size:28px;
        line-height:1.2;
    }
    .brand-logo {
        width: 120px;
        margin-top: -25px;
    }
    .button-group {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        gap: 16px;
    }
    .btn-hero {
        width: 100%;
        max-width: 270px;
        min-width: unset;
        padding: 13px 20px;
        font-size: 15px;
        border-radius: 12px;
    }
    .footer-text {
        font-size: 11px;
        bottom: 18px;
        padding: 0 18px;
    }
    .carousel,
    .carousel-inner,
    .carousel-item{
        height:100%;
    }

    .carousel-item img{
        width:100%;
        height:100%;
        object-fit:cover;
    }
}
</style>
<section class="hero">
    <div id="heroSlider" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('images/hero-dekstop.jpeg') }}">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/coba-hero.jpeg') }}">
            </div>
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-caption">

            <h3>
                Sebuah Ruang untuk Bertumbuh
                <br>
                Sebuah Ruang untuk Kembali
            </h3>

        </div>

        <button class="carousel-control-prev"
                data-bs-target="#heroSlider"
                data-bs-slide="prev">

            <span class="carousel-control-prev-icon"></span>

        </button>

        <button class="carousel-control-next"
                data-bs-target="#heroSlider"
                data-bs-slide="next">

            <span class="carousel-control-next-icon"></span>

        </button>

        <div class="carousel-indicators">

            <button data-bs-target="#heroSlider"
                    data-bs-slide-to="0"
                    class="active"></button>

            <button data-bs-target="#heroSlider"
                    data-bs-slide-to="1"></button>

        </div>

    </div>

</section>

@endsection