<template>
    <div class="font-alegreya">
        <!--ハンバーガーメニューのリンク-->
        <div class="hamburger_btn" v-on:click="naviOpen">
            <span class="line line_01" v-bind:class="{'btn_line01':active}"></span>
            <span class="line line_02" v-bind:class="{'btn_line02':active}"></span>
            <span class="line line_03" v-bind:class="{'btn_line03':active}"></span>
        </div>
        <!--サイドバー-->
        <transition name="menu">
            <div v-show="active">
                <div class="menu-bg"></div>
                <div class="menu">
                    <ul>
                        <li><a href="/home">HOME</a></li>
                        <li><a href="/news">News</a></li>
                        <li><a href="/ranking">Ranking</a></li>
                        <li><a href="/favorite_brand">Favorite  Brand</a></li>
                        <li><a href="/favorite_player">Favorite Player</a></li>
                        <li>
                            <a href="/logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="/logout" method="POST" style="display: none;">
                                <input type="hidden" name="_token" :value="csrf_token">
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
var csrf_token = $('meta[name="csrf-token"]').attr('content');

export default {
    data() {
        return {
            csrf_token: csrf_token,
            active: false
        }
    },
    methods: {
        naviOpen: function() {
            this.active = !this.active;
        }
    }
}
</script>

<style lang="scss" scoped>
/*ボタン*/
.hamburger_btn {
    position: fixed; /*常に最上部に表示したいので固定*/
    top: 0;
    right: 0;
    width: 43px;
    height: 45px;
    cursor: pointer;
    z-index: 50;
    background-color: black;
    margin-top: 13px;
    margin-right: 13px;
    border-radius: 10px;
}

.hamburger_btn .line {
    position: absolute;
    top: 0;
    left: 9px;
    width: 27px;
    height: 2px;
    background: #d5d5d5;
    text-align: center;
}

.hamburger_btn .line_01 {
    top: 12px;
    transition: 0.4s ease;
}
.hamburger_btn .line_02 {
    top: 22px;
    transition: 0.4s ease;
}
.hamburger_btn .line_03 {
    top: 32px;
    transition: 0.4s ease;
}

.btn_line01 {
    transform: translateY(10px) rotate(-45deg);
    transition: 0.4s ease;
}
.btn_line02 {
transition: 0.4s ease;
    opacity: 0;
}
.btn_line03 {
    transform: translateY(-10px) rotate(45deg);
    transition: 0.4s ease;
}

/*サイドバー*/
.menu-enter-active, .menu-leave-active {
    transition: opacity 0.4s;
}
.menu-enter, .menu-leave-to {
    opacity: 0;
}
.menu-leave, .menu-enter-to{
    opacity: 1;
}
.menu li {
    list-style: none;
    line-height: 1;
    padding: 0 14px;
}
.menu {
    background-color: #c8c8c8;
    z-index: 30;
    padding: 2rem 1rem;
    position: fixed;
    width: 17rem;
    height: 80rem;
    top: 0;
    right: 0;
}
.menu a {
    display: block;
    border-bottom: 1px solid #eee;
    color: rgb(85, 84, 84);
    padding: 10px 0;
    text-decoration: none;
    font-size: 1.2rem;
    text-align: center;
}
.menu a:hover {
    background-color: #eee;
}
.menu ul{
    padding-top: 35px;
}
.menu-bg {
    position: fixed;
    top: 0;
    right: 0;
    opacity: 0.6;
    background-color: black;
    width: 100vw;
    height: 100vh;
}
</style>