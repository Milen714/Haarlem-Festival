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

	// Display properties for event listings
	public ?string $genres = null;
	public ?bool $is_headliner = null;
	public ?int $performance_order = null;

	public function __construct() {}

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
		$this->deleted_at = isset($data['deleted_at']) ? new DateTime($data['deleted_at']) : null;

		// Genre and event data
		$this->genres = $data['genres'] ?? null;
		$this->is_headliner = isset($data['is_headliner']) ? (bool)$data['is_headliner'] : null;
		$this->performance_order = isset($data['performance_order']) ? (int)$data['performance_order'] : null;

		if (isset($data['profile_image_id']) || isset($data['media_id'])) {
			$this->profile_image = new Media();
			$this->profile_image->fromPDOData([
				'media_id' => $data['profile_image_id'] ?? $data['media_id'] ?? null,
				'file_path' => $data['profile_image_path'] ?? ($data['file_path'] ?? null),
				'alt_text' => $data['profile_image_alt_text'] ?? ($data['alt_text'] ?? null),
			]);
		}

		if (isset($data['gallery_id'])) {
			$this->gallery = new Gallery();
			$this->gallery->fromPDOData([
				'gallery_id' => $data['gallery_id'],
				'gallery_title' => $data['gallery_title'] ?? null,
				'created_at' => $data['gallery_created_at'] ?? ($data['created_at'] ?? null),
			]);
		}
	}

	public function fromPostData(array $data): void
	{
		$artistId = $data['artist_id'] ?? null;
		$this->artist_id = ($artistId === null || $artistId === '') ? null : (int)$artistId;
		$this->name = $data['name'] ?? '';
		$this->slug = $data['slug'] ?? '';
		$this->bio = $data['bio'] ?? '';
		$this->website = $data['website'] ?? '';
		$this->spotify_url = $data['spotify_url'] ?? '';
		$this->youtube_url = $data['youtube_url'] ?? '';
		$this->soundcloud_url = $data['soundcloud_url'] ?? '';
		$this->featured_quote = $data['featured_quote'] ?? '';
		$this->press_quote = $data['press_quote'] ?? '';
		$this->collaborations = $data['collaborations'] ?? '';
		$this->deleted_at = !empty($data['deleted_at']) ? new DateTime($data['deleted_at']) : null;

		// Genre and event data from POST
		$this->genres = $data['genres'] ?? null;
		$this->is_headliner = isset($data['is_headliner']) ? (bool)$data['is_headliner'] : null;
		$this->performance_order = isset($data['performance_order']) ? (int)$data['performance_order'] : null;

		$profileImageId = $data['profile_image_id'] ?? null;
		if ($profileImageId !== null && $profileImageId !== '') {
			$this->profile_image = new Media();
			$this->profile_image->fromPostData([
				'media_id' => $profileImageId,
				'file_path' => $data['profile_image_path'] ?? ($data['file_path'] ?? null),
				'alt_text' => $data['profile_image_alt_text'] ?? ($data['alt_text'] ?? null),
			]);
		}

		$galleryId = $data['gallery_id'] ?? null;
		if ($galleryId !== null && $galleryId !== '') {
			$this->gallery = new Gallery();
			$this->gallery->fromPostData([
				'gallery_id' => $galleryId,
				'title' => $data['gallery_title'] ?? ($data['title'] ?? null),
			]);
		}
	}
}