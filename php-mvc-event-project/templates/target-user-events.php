<?php include 'header.php'; ?>

<main class="flex-grow py-8 md:py-12 px-4 sm:px-6 md:px-8 max-w-7xl mx-auto w-full">

    <div class="mb-6 md:mb-8 border-b border-gray-200 pb-4">
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 flex flex-col md:flex-row md:items-baseline flex-wrap gap-1 md:gap-2">
            <span>กิจกรรมที่สร้างโดย:</span>
            <span class="text-blue-600 truncate max-w-full">
                <?= htmlspecialchars($creatorName) ?>
            </span>
            <?php if (!empty($creatorEmail)): ?>
                <span class="text-base md:text-lg text-gray-500 font-normal break-all">
                    (<?= htmlspecialchars($creatorEmail) ?>)
                </span>
            <?php endif; ?>
        </h1>
    </div>

    <?php if (empty($events)) : ?>
        <div class="no-data bg-white p-8 md:p-12 rounded-xl shadow-sm border border-gray-200 text-center mx-auto max-w-2xl mt-4 md:mt-8">
            <p class="text-gray-500 text-base md:text-lg font-medium">ผู้ใช้งานรายนี้ยังไม่มีกิจกรรม</p>
        </div>
    <?php else : ?>
        <div class="event-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6">
            <?php foreach ($events as $event) : ?>
                <div class="event-card bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-blue-400 transition-all flex flex-col overflow-hidden h-full">

                    <div class="group h-48 w-full bg-gray-100 overflow-hidden border-b border-gray-100 relative">
                        <?php
                        $images = getImagesByEventId($event['id']);
                        if (!empty($images)) :
                        ?>
                            <div class="image-slider flex overflow-x-auto snap-x snap-mandatory h-full w-full scrollbar-hide" style="-ms-overflow-style: none; scrollbar-width: none;">
                                <?php foreach ($images as $img) : ?>
                                    <img src="<?= htmlspecialchars($img) ?>" alt="Event Image" class="w-full h-full object-cover flex-shrink-0 snap-center">
                                <?php endforeach; ?>
                            </div>

                            <?php if (count($images) > 1): ?>
                                <button type="button" onclick="slideImage(this, 'left')"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/70 text-white p-2 rounded-full backdrop-blur-sm transition-all shadow-md z-10 opacity-0 group-hover:opacity-100 focus:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                    </svg>
                                </button>
                                <button type="button" onclick="slideImage(this, 'right')"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/70 text-white p-2 rounded-full backdrop-blur-sm transition-all shadow-md z-10 opacity-0 group-hover:opacity-100 focus:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>
                            <?php endif; ?>

                        <?php else : ?>
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <span class="text-3xl mb-1">🖼️</span>
                                <span class="text-xs">No Image</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="p-5 md:p-6 flex flex-col flex-grow">
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                            <?= htmlspecialchars($event['name'] ?? 'ไม่มีชื่อกิจกรรม') ?>
                        </h3>

                        <div class="mb-4 pb-3 border-b border-gray-100">
                            <span class="inline-flex flex-wrap items-center gap-1 bg-blue-50 text-blue-800 text-xs px-2.5 py-1.5 rounded-lg font-semibold w-full">
                                <span>👤 โดย: <?= htmlspecialchars($event['creator_name']) ?></span>
                                <?php if (!empty($event['creator_email'] ?? $event['email'])): ?>
                                    <span class="font-normal opacity-80 break-all w-full sm:w-auto mt-0.5 sm:mt-0">(<?= htmlspecialchars($event['creator_email'] ?? $event['email']) ?>)</span>
                                <?php endif; ?>
                            </span>
                        </div>

                        <p class="text-gray-600 text-sm mb-5 leading-relaxed flex-grow">
                            <strong class="text-gray-800 font-semibold block mb-1">รายละเอียด:</strong>
                            <?php
                            $desc = $event['event_description'] ?? $event['description'] ?? 'ไม่มีรายละเอียด';
                            echo mb_strlen($desc) > 80 ? mb_substr(htmlspecialchars($desc), 0, 80) . '...' : htmlspecialchars($desc);
                            ?>
                        </p>

                        <div class="space-y-1 mb-5 md:mb-6">
                            <div class="text-gray-700 text-sm m-0 bg-blue-50 px-3 md:px-4 py-2 rounded-t-lg border-b border-blue-100 flex flex-col sm:flex-row sm:items-center">
                                <strong class="text-blue-800 font-semibold w-full sm:w-20 mb-0.5 sm:mb-0">เวลาเริ่ม:</strong>
                                <span><?= htmlspecialchars($event['event_start'] ?? '-') ?></span>
                            </div>
                            <div class="text-gray-700 text-sm m-0 bg-blue-50 px-3 md:px-4 py-2 rounded-b-lg flex flex-col sm:flex-row sm:items-center">
                                <strong class="text-blue-800 font-semibold w-full sm:w-20 mb-0.5 sm:mb-0">เวลาจบ:</strong>
                                <span><?= htmlspecialchars($event['event_end'] ?? '-') ?></span>
                            </div>
                        </div>

                        <a href="/events/<?= $event['id'] ?? $event['event_id'] ?? '' ?>/detail" class="btn block text-center w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors shadow-sm mt-auto no-underline">
                            ดูรายละเอียดกิจกรรม
                        </a>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<script>
    function slideImage(buttonElement, direction) {
        const container = buttonElement.parentElement.querySelector('.image-slider');
        if (container) {
            const scrollAmount = container.clientWidth;
            if (direction === 'left') {
                container.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            } else {
                container.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            }
        }
    }
</script>

<?php include 'footer.php'; ?>