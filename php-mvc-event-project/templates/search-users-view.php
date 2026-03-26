<?php include 'header.php' ?>
<main class="flex-grow pb-10">
    <h3 class="text-2xl font-bold text-gray-800 mb-4 mt-8 px-4 sm:px-8">ค้นหาผู้ใช้งาน (Search Users)</h3>

    <div class="px-4 sm:px-8 mb-8">
        <form action="/newfuture/search-users" method="GET" class="flex flex-col md:flex-row w-full gap-3 md:items-end">
            <div class="relative w-full md:w-96">
                <label class="block text-xs font-semibold text-gray-500 mb-1 ml-1 uppercase">ค้นหาจาก Username หรือ Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-400">🔍</span>
                    </div>
                    <input type="text" name="keyword" placeholder="กรอกชื่อผู้ใช้ หรือ อีเมล..." value="<?= htmlspecialchars($keyword ?? '') ?>"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all shadow-sm" />
                </div>
            </div>
            <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl font-bold transition-all shadow-sm">
                ค้นหา
            </button>
            <?php if (!empty($keyword)): ?>
                <a href="/newfuture/search-users" class="text-sm text-center md:text-left text-gray-400 hover:text-red-500 mb-1 md:mb-3 px-2 w-full md:w-auto">เคลียร์</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($searched_users)) : ?>
        <p class="text-gray-500 bg-white p-8 rounded-lg border border-gray-200 text-center mx-4 sm:mx-8 shadow-sm">ไม่พบผู้ใช้งานที่ค้นหา</p>
    <?php else : ?>
        <ul class="px-4 sm:px-8 pb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 m-0 p-0">
            <?php foreach ($searched_users as $user) : ?>
                <?php
                // คำนวณอายุจาก birthdate
                $age = "ไม่ระบุ";
                if (!empty($user['birthdate'])) {
                    $birthDate = new DateTime($user['birthdate']);
                    $today = new DateTime('today');
                    $age = $birthDate->diff($today)->y . " ปี";
                }

                // กำหนดไอคอนเพศ
                $gender_icon = '❓';
                if (strtolower($user['gender'] ?? '') == 'm' || strtolower($user['gender'] ?? '') == 'male') $gender_icon = '👨';
                elseif (strtolower($user['gender'] ?? '') == 'f' || strtolower($user['gender'] ?? '') == 'female') $gender_icon = '👩';
                ?>
                <li class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all list-none p-6 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="text-4xl"><?= $gender_icon ?></div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 leading-tight"><?= htmlspecialchars($user['username'] ?? $user['email']) ?></h4>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($user['name'] ?? 'ไม่ระบุชื่อ') ?></p>
                            </div>
                        </div>

                        <div class="text-sm text-gray-600 space-y-2 border-t border-gray-100 pt-3">
                            <?php
                            $ageDisplay = "ไม่ระบุ";
                            if (!empty($user['birthday'])) {
                                $birthDate = new DateTime($user['birthday']);
                                $today = new DateTime('today');
                                $age = $birthDate->diff($today)->y; // คำนวณความต่างของจำนวนปี
                                $ageDisplay = $age . " ปี";
                            }
                            ?>
                            <div class="text-sm text-gray-600 mb-1">
                                อายุ: <?= $ageDisplay ?>
                            </div>
                            <p>
                                <strong>จำนวนกิจกรรมที่สร้าง:</strong>
                                <?php if ($user['created_events_count'] > 0): ?>
                                    <span class="text-blue-600 font-bold"><?= $user['created_events_count'] ?> กิจกรรม</span>
                                <?php else: ?>
                                    <span class="text-red-500">ไม่มีกิจกรรมที่สร้าง</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <?php if ($user['created_events_count'] > 0): ?>
                        <a href="/newfuture/user-events?target_id=<?= htmlspecialchars((string)$user['user_id']) ?>" class="block w-full text-center bg-gray-100 hover:bg-blue-100 text-gray-800 hover:text-blue-700 font-bold py-2.5 rounded-lg transition-colors border border-gray-300 hover:border-blue-400 mt-4">
                            ดูข้อมูลกิจกรรม (<?= $user['created_events_count'] ?>)
                        </a>
                    <?php else: ?>
                        <button disabled class="block w-full text-center bg-gray-50 text-gray-400 font-bold py-2.5 rounded-lg border border-gray-200 cursor-not-allowed mt-4">
                            ไม่มีกิจกรรม
                        </button>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
<?php include 'footer.php' ?>