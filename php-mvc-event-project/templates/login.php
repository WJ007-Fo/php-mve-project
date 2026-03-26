<?php include 'header.php' ?>

<main class="flex-grow flex flex-col items-center justify-center px-4 py-8 md:py-16">
    <div class="w-full max-w-md">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">เข้าสู่ระบบ</h1>

        <form action="/users/login" method="post" class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100 transition-all">

            <div class="mb-5">
                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">อีเมลผู้ใช้</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-400"
                    placeholder="example@mail.com" />
            </div>

            <div class="mb-8">
                <label for="password" class="block text-sm font-bold text-gray-700 mb-2">รหัสผ่าน</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-400"
                    placeholder="••••••••" />
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-lg">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-md transform active:scale-[0.98]">
                เข้าสู่ระบบ
            </button>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-600">
                    หากยังไม่มีบัญชีผู้ใช้?
                    <a href="/users/register" class="text-blue-600 hover:text-blue-800 font-bold ml-1 transition-colors">สมัครสมาชิก</a>
                </p>
            </div>
        </form>
    </div>
</main>

<?php include 'footer.php' ?>