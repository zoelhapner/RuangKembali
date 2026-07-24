<header class="website-header">

    <div class="website-navbar">
        <div class="website-top">
            <a href="/" class="website-logo">
                <img src="{{ asset('images/logo-landscape.png') }}" alt="Ruang Kembali">
            </a>
            <div class="website-menu-scroll">
                <nav class="website-menu">

                    <a href="/">HOME</a>

                    <a href="/artikel">ARTIKEL</a>
                    <a href="/event">PRODUK</a>
                    <div class="menu-dropdown">

                        <button class="menu-link">
                            EVENT
                            <i class="ti ti-chevron-down"></i>
                        </button>

                        <div class="mega-menu">

                            <a href="#">Bisik Lirih</a>
                            <a href="#">Bukti Cinta Sang Idola</a>
                            <a href="#">The 30 Days Race</a>
                            <a href="#">Bisik Lirih Podcast</a>
                            <a href="#">Ruka Movement</a>
                            <a href="#">Ruka Bazaar</a>

                        </div>

                    </div>

                    <a href="{{ route('register') }}">BERGABUNG</a>

                    <a href="/tentang-kami">TENTANG KAMI</a>

                </nav>
            </div>
            <div class="website-icons">

                <a href="#"><i class="ti ti-search"></i></a>

                <a href="#"><i class="ti ti-shopping-bag"></i></a>

                <div class="user-dropdown">

                    <button class="user-btn">
                        <i class="ti ti-user"></i>
                    </button>

                    <div class="user-menu">

                        @guest
                            <a href="{{ route('login') }}">
                                <i class="ti ti-login"></i>
                                Masuk
                            </a>

                            <a href="{{ route('register') }}">
                                <i class="ti ti-user-plus"></i>
                                Daftar
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}">
                                <i class="ti ti-layout-dashboard"></i>
                                Dashboard
                            </a>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit">
                                    <i class="ti ti-logout"></i>
                                    Logout
                                </button>
                            </form>
                        @endguest

                    </div>

                </div>

            </div>
        </div>
    </div>
</header>
<style>
.website-header{
    position:absolute;
    top:25px;
    left:0;
    width:100%;
    z-index:1000;
}
.website-navbar{
    width:min(92%,1500px);
    margin:auto;
    background:#fff;
    border-radius:22px;
    box-shadow:0 15px 40px rgba(0,0,0,.15);
    height:86px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 40px;
}
.website-logo img{
   height:48px;
    display:block;
}
.website-menu{
    display:flex;
    align-items:center;
    gap:38px;
}
.website-top{
    display:contents;
}
.website-menu>a,
.menu-link{
    background:none;
    border:none;
    color:#111;
    text-decoration:none;
    font-size:15px;
    font-weight:600;
    letter-spacing:.4px;
    cursor:pointer;
    display:flex;
    align-items:center;
    gap:5px;
    transition:.25s;
}
.website-menu>a:hover,
.menu-link:hover{
    color:#b7965b;
}
.website-icons{
    display:flex;
    align-items:center;
    gap:22px;
}
.website-icons a,
.user-btn{
    background:none;
    border:none;
    color:#111;
    font-size:28px;
    cursor:pointer;
    text-decoration:none;
}
.user-dropdown{
    position:relative;
}
.user-menu{
    position:absolute;
    top:45px;
    right:0;
    width:190px;
    background:#fff;
    border-radius:12px;
    box-shadow:0 15px 35px rgba(0,0,0,.15);
    display:none;
    overflow:hidden;
    z-index:9999;
}
.user-menu a,
.user-menu button{
    width:100%;
    padding:14px 18px;
    display:flex;
    align-items:center;
    gap:10px;
    background:none;
    border:none;
    text-align:left;
    color:#111;
    text-decoration:none;
    cursor:pointer;
    font-size:15px;
}
.user-menu a:hover,
.user-menu button:hover{
    background:#f5f5f5;
}
.user-dropdown:hover .user-menu{
    display:block;
}
.menu-dropdown{
   position:relative;
}
.mega-menu{
    position:absolute;
    top:55px;
    left:50%;
    transform:translateX(-50%);
    width:360px;
    background:#DCCBA8;
    display:none;
    box-shadow:0 15px 35px rgba(0,0,0,.18);
}
.menu-dropdown:hover .mega-menu{
    display:block;
}
.mega-menu a{
    display:block;
    padding:18px 28px;
    color:#111;
    text-decoration:none;
    font-weight:600;
    border-bottom:1px solid rgba(0,0,0,.15);
}
.mega-menu a:last-child{
    border-bottom:none;
}
.mega-menu a:hover{
    background:#c3bbb0;
}
@media (max-width:768px){

    .website-navbar{

        width:95%;

        height:auto;

        padding:14px 18px;

        display:flex;

        flex-direction:column;

        align-items:stretch;

        gap:12px;
    }

    .website-top{

        display:flex;

        justify-content:space-between;

        align-items:center;
    }

    .website-logo img{

        height:38px;
    }

    .website-icons{

        display:flex;

        align-items:center;

        gap:18px;
    }

    .website-menu{

        order:2;

        display:flex;

        align-items:center;

        gap:24px;

        overflow-x:auto;

        overflow-y:hidden;

        white-space:nowrap;

        padding-bottom:6px;

        scrollbar-width:none;

        border-top:1px solid #eee;

        padding-top:12px;
    }

    .website-menu::-webkit-scrollbar{

        display:none;
    }

    .website-menu>a,
    .menu-link{

        font-size:13px;

        font-weight:600;

        flex-shrink:0;
    }

}
@media (max-width:768px){

    .mega-menu{

        position:fixed;

        left:20px;

        right:20px;

        top:120px;

        width:auto;

        display:none;

        border-radius:12px;
    }

    .menu-dropdown.open .mega-menu{

        display:block;
    }

}
</style>