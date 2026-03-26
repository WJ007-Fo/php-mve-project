<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title'] ?? 'Event System') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* สไตล์สำหรับอนิเมชั่นตอนเปิดเมนู */
        #mobile-menu.hidden {
            display: none;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen bg-gray-50 text-gray-800">

    <nav class="bg-white border-b border-gray-200 px-4 sm:px-8 py-4 shadow-sm w-full sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between">

            <a href="/" class="text-xl font-extrabold text-blue-600 no-underline hover:text-blue-700 transition-colors">
                <?= htmlspecialchars($data['title'] ?? 'Event System') ?>
            </a>

            <button id="menu-btn" class="lg:hidden flex flex-col justify-center items-center w-10 h-10 border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none">
                <span class="w-6 h-0.5 bg-gray-600 mb-1.5 transition-all"></span>
                <span class="w-6 h-0.5 bg-gray-600 mb-1.5 transition-all"></span>
                <span class="w-6 h-0.5 bg-gray-600 transition-all"></span>
            </button>

            <div class="hidden lg:flex items-center gap-6">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/events" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">Explore</a>
                    <a href="/join_event/my-registers" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">My Register</a>
                    <a href="/events/my-event" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">My Events</a>
                    <a href="/users/profile" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">Profile</a>
                    <a href="/newfuture/search-users" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">NewFuture</a>
                    <a href="/users/logout" class="text-red-500 hover:text-white hover:bg-red-500 font-medium px-4 py-1.5 rounded-lg border border-red-500 transition-all">ออกจากระบบ</a>
                <?php else: ?>
                    <a href="/events" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">Explore</a>
                    <a href="/newfuture/search-users" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">NewFuture</a>
                    <a href="/users/login" class="text-blue-600 font-bold px-2 border-l border-gray-300">เข้าสู่ระบบ</a>
                    <a href="/users/register" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg border border-blue-600 transition-all">สมัครสมาชิก</a>
                <?php endif; ?>
            </div>
        </div>

        <div id="mobile-menu" class="hidden lg:hidden flex flex-col gap-2 mt-4 pt-4 border-t border-gray-100">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/events" class="p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">Explore</a>
                <a href="/join_event/my-registers" class="p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">My Register</a>
                <a href="/events/my-event" class="p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">My Events</a>
                <a href="/users/profile" class="p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">Profile</a>
                <a href="/newfuture/search-users" class="p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors font-bold text-blue-600">NewFuture</a>
                <div class="border-t border-gray-100 my-2"></div>
                <a href="/users/logout" class="p-3 text-red-500 font-bold hover:bg-red-50 rounded-lg transition-colors text-center border border-red-100 mt-2">ออกจากระบบ</a>
            <?php else: ?>
                <a href="/events" class="p-3 text-gray-700 hover:bg-blue-50 rounded-lg">Explore</a>
                <a href="/newfuture/search-users" class="p-3 text-gray-700 hover:bg-blue-50 rounded-lg">NewFuture</a>
                <div class="border-t border-gray-100 my-2"></div>
                <a href="/users/login" class="p-3 text-blue-600 font-bold text-center">เข้าสู่ระบบ</a>
                <a href="/users/register" class="p-3 bg-blue-600 text-white font-bold rounded-lg text-center shadow-md">สมัครสมาชิก</a>
            <?php endif; ?>
        </div>
    </nav>

    <script>
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // คลิกพื้นที่อื่นในหน้าจอเพื่อปิดเมนูอัตโนมัติ (เผื่อผู้ใช้ลืมปิด)
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) { // 1024px คือขนาด lg ของ Tailwind
                mobileMenu.classList.add('hidden');
            }
        });
    </script>