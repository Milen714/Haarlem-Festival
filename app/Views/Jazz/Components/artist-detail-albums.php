<?php

?>

<section aria-labelledby="albums-heading">
    <h2 id="albums-heading" class="text-3xl font-bold mb-6" style="font-family: 'Cormorant Garamond', serif;">
        Albums
    </h2>

    <ul class="grid grid-cols-2 md:grid-cols-3 gap-6">
        <?php foreach ($artist->albums as $album): ?>
            <?php
                $coverPath = '';
                if ($album->cover_image && !empty($album->cover_image->file_path)) {
                    $coverPath = $album->cover_image->file_path;
                    $coverPath = str_starts_with($coverPath, '/') ? $coverPath : '/' . $coverPath;
                }
            ?>
            <li>
                <figure class="bg-white rounded-xl shadow hover:shadow-lg transition-shadow overflow-hidden">
                    <?php if ($coverPath): ?>
                    <img src="<?= htmlspecialchars($coverPath) ?>"
                         alt="<?= htmlspecialchars($album->name ?? '') ?> album cover"
                         class="w-full h-48 object-cover" />
                    <?php endif; ?>
                    <figcaption class="p-4">
                        <h3 class="font-bold text-lg"><?= htmlspecialchars($album->name ?? '') ?></h3>
                        <?php if (!empty($album->release_year)): ?>
                            <p class="text-sm text-gray-500">
                                <time datetime="<?= (int)$album->release_year ?>"><?= htmlspecialchars($album->release_year) ?></time>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($album->spotify_url)): ?>
                            <a href="<?= htmlspecialchars($album->spotify_url) ?>" target="_blank" rel="noopener noreferrer"
                                class="mt-2 inline-block text-sm text-green-600 hover:underline">
                                Listen on Spotify
                            </a>
                        <?php endif; ?>
                    </figcaption>
                </figure>
            </li>
        <?php endforeach; ?>
    </ul>
</section>