<header class="website-header">
    <div class="website-navbar">
        <a href="/" class="website-logo">
            <img src="{{ asset('images/logo-landscape.png') }}" alt="Ruang Kembali">
        </a>
        <nav class="website-menu" id="websiteMenu">
            <a href="/">HOME</a>
            <a href="#">ARTIKEL</a>
            <a href="#">PRODUK</a>
            <div class="menu-dropdown">
                <button type="button" class="menu-link">
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
            <a href="#">TENTANG KAMI</a>
        </nav>
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
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="ti ti-menu-2"></i>
            </button>
        </div>
    </div>

</header>
<div class="mobile-overlay" id="mobileOverlay"></div>
<style>
.website-header{
    position:fixed;
    top:20px;
    left:0;
    width:100%;
    z-index:2;
}
.website-navbar{

    width:min(92%,1500px);

    margin:auto;

    background:#fff;

    border-radius:22px;

    box-shadow:0 15px 40px rgba(0,0,0,.15);

    height:86px;

    display:flex;
    justify-content:space-between;
    align-items:center;

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
.mobile-menu-btn{
    display:none;
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

    z-index:99;
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

/* .menu-dropdown:hover .mega-menu{

    display:block;

} */
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

.website-header{
    top:15px;
    z-index:9999;
}

.website-navbar{

    width:calc(100% - 24px);

    height:64px;

    padding:0 16px;

    border-radius:18px;

}

.website-logo{

    flex:1;

}

.website-logo img{

    height:34px;

}

.website-icons{

    display:flex;

    align-items:center;

    gap:14px;

}

/* .mobile-menu-btn{

    display:flex;

    border:none;

    background:none;

    font-size:28px;

    cursor:pointer;

} */
.mobile-menu-btn{
    display:flex;
    align-items:center;
    justify-content:center;
    width:44px;
    height:44px;
    z-index:10002;
        border:none;
        cursor:pointer;
    background:none;
}

.website-menu{

    position:fixed;

    top:0;

    right:-320px;

    width:320px;

    max-width:85%;

    height:100vh;

    background:#fff;

    display:flex;

    flex-direction:column;

    gap:0;  
    align-items:stretch;   /* tambahkan */

    justify-content:flex-start;
    padding:90px 24px;

    transition:.35s;
     pointer-events:auto;
    z-index:10001;

}

.website-menu.active{

    right:0;

}

.website-menu>a,
.menu-link{

    width:100%;

    padding:18px 0;

    display:flex;

    justify-content:space-between;

    border-bottom:1px solid #eee;

}

.mobile-overlay{

    position:fixed;

    inset:0;
    background:rgba(0,0,0,.45);

    opacity:0;

    visibility:hidden;

    transition:.3s;
    pointer-events:none;
    z-index:10000;

}

.mobile-overlay.show{

    opacity:1;

    visibility:visible;
     pointer-events:auto;
}

.mega-menu{

    position:static;

    display:none;

    width:100%;

    background:#f5f5f5;

    transform:none;

    box-shadow:none;

}

.menu-dropdown.open .mega-menu{

    display:block;

}

.menu-dropdown:hover .mega-menu{

    display:none;

}

}
</style>
<script>
const menu = document.getElementById("websiteMenu");

const btn = document.getElementById("mobileMenuBtn");

const overlay = document.getElementById("mobileOverlay");

btn.onclick = () => {

    menu.classList.toggle("active");

    overlay.classList.toggle("show");

};

overlay.onclick = () => {

    menu.classList.remove("active");

    overlay.classList.remove("show");

};

document.querySelectorAll(".menu-link").forEach(btn=>{

    btn.addEventListener("click",function(e){

        if(window.innerWidth>768) return;

        e.preventDefault();
        e.stopPropagation();

        this.closest(".menu-dropdown").classList.toggle("open");

    });

});
window.addEventListener('resize', function () {

    if (window.innerWidth > 768) {
        menu.classList.remove('active');
        overlay.classList.remove('show');

        document.querySelectorAll('.menu-dropdown')
            .forEach(item => item.classList.remove('open'));
    }

});
</script>