<?php
namespace App\Models\MusicEvent;

use App\Models\Media;
use App\Models\Gallery;
use DateTime;

class Artist
{
	public ?int $artist_id = null;
	public ?string $name = null;
	public ?string $slug = null;
	public ?string $bio = null;
	public ?Media $profile_image = null;
	public ?Gallery $gallery = null;
	public ?string $website = null;
	public ?string $spotify_url = null;
	public ?string $youtube_url = null;
	public ?string $soundcloud_url = null;
	public ?string $featured_quote = null;
	public ?string $press_quote = null;
	public ?string $collaborations = null;
	public ?DateTime $deleted_at = null;

	// Displays the  properties for event listings
	public ?string $genres = null;
	public ?bool $is_headliner = null;
	public ?int $performance_order = null;

	public function __construct() {}

	
	public function getProfileImagePath(): string
	{
		if ($this->profile_image && $this->profile_image->file_path) {
			$path = $this->profile_image->file_path;
			return str_starts_with($path, '/') ? $path : '/' . $path;
		}
		
		return '/Assets/Home/ImagePlaceholder.png';
	}

	
	public function getProfileImageAlt(): string
	{
		return $this->profile_image?->alt_text ?? $this->name ?? 'Artist';
	}

		public function getInitial(): string
	{
		return strtoupper(substr($this->name ?? 'A', 0, 1));
	}

	
	public function hasProfileImage(): bool
	{
		return $this->profile_image !== null && !empty($this->profile_image->file_path);
	}

	public function fromPDOData(array $data): void
{
    $this->artist_id = isset($data['artist_id']) ? (int)$data['artist_id'] : null;
    $this->name = $data['name'] ?? null;
    $this->slug = $data['slug'] ?? null;
    $this->bio = $data['bio'] ?? null;
    $this->website = $data['website'] ?? null;
    $this->spotify_url = $data['spotify_url'] ?? null;
    $this->youtube_url = $data['youtube_url'] ?? null;
    $this->soundcloud_url = $data['soundcloud_url'] ?? null;
    $this->featured_quote = $data['featured_quote'] ?? null;
    $this->press_quote = $data['press_quote'] ?? null;
    $this->collaborations = $data['collaborations'] ?? null;

    if (isset($data['deleted_at']) && $data['deleted_at']) {
        $this->deleted_at = new DateTime($data['deleted_at']);
    }

    $this->genres = $data['genres'] ?? null;
    $this->is_headliner = isset($data['is_headliner']) ? (bool)$data['is_headliner'] : null;
    $this->performance_order = isset($data['performance_order']) ? (int)$data['performance_order'] : null;

    $mediaId = $data['artist_profile_image_id'] ?? $data['media_id'] ?? null;
    $filePath = $data['artist_profile_image_path'] ?? $data['file_path'] ?? null;
    $altText = $data['artist_profile_image_alt'] ?? $data['alt_text'] ?? null;
    
    if ($mediaId && $filePath) {
        $this->profile_image = new Media();
        $this->profile_image->fromPDOData([
            'media_id' => $mediaId,
            'file_path' => $filePath,
            'alt_text' => $altText,
        ]);
    }

    if (isset($data['artist_gallery_id'])) {
        $this->gallery = new Gallery();
        $this->gallery->fromPDOData([
            'gallery_id' => $data['artist_gallery_id'],
            'gallery_title' => $data['artist_gallery_title'] ?? null,
        ]);
    }
}
}