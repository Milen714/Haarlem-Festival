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
	public ?bool $special_event = false;
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

	public array $albums = [];
	public array $artistGenres = [];

	public ?string $genres = null;
	public ?bool $is_headliner = null;
	public ?int $performance_order = null;

	public function __construct() {}

	public static function createFromPostData(array $data): self
	{
		$artist = new self();
		$artist->applyPostData($data);

		return $artist;
	}

	/**
	 * Applies form POST data onto an existing Artist instance.
	 * Trims all string fields, generates a URL slug from the name,
	 * and sets optional fields to null if they are empty.
	 * Use this when updating an existing artist (e.g. in the CMS update action).
	 */
	public function applyPostData(array $data): void
	{
		$this->name = trim($data['name']);
		$this->slug = self::generateSlug($this->name);
		$this->special_event = isset($data['special_event']) ? (bool)$data['special_event'] : false;
		$this->bio = self::optionalTrimmedValue($data, 'bio');
		$this->featured_quote = self::optionalTrimmedValue($data, 'featured_quote');
		$this->website = self::optionalTrimmedValue($data, 'website');
		$this->spotify_url = self::optionalTrimmedValue($data, 'spotify_url');
		$this->youtube_url = self::optionalTrimmedValue($data, 'youtube_url');
		$this->soundcloud_url = self::optionalTrimmedValue($data, 'soundcloud_url');
		$this->press_quote = self::optionalTrimmedValue($data, 'press_quote');
		$this->collaborations = self::optionalTrimmedValue($data, 'collaborations');
	}

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

	/**
	 * Returns the first letter of the artist's name in uppercase.
	 * Used as a visual placeholder (avatar initial) when no profile image is available.
	 * Defaults to 'A' if the name is not set.
	 */
	public function getInitial(): string
	{
		return strtoupper(substr($this->name ?? 'A', 0, 1));
	}

	public function hasProfileImage(): bool
	{
		return $this->profile_image !== null && !empty($this->profile_image->file_path);
	}

	/**
	 * Maps a raw database row (PDO result array) onto this Artist instance.
	 * Handles column aliasing from SQL JOINs — columns may come in as e.g.
	 * 'artist_name' or 'name' depending on the query. Both are checked.
	 * Also hydrates the nested Media (profile image) and Gallery objects
	 * if the relevant columns are present in the row.
	 */
	public function fromPDOData(array $data): void
	{
		$this->artist_id = isset($data['artist_id']) ? (int)$data['artist_id'] : null;
		$this->name = $data['name'] ?? $data['artist_name'] ?? null;
		$this->slug = $data['artist_slug'] ?? $data['slug'] ?? null;
		$this->special_event = isset($data['special_event']) ? (bool)$data['special_event'] : (isset($data['special_event']) ? (bool)$data['special_event'] : null);
		$this->bio = $data['artist_bio'] ?? $data['bio'] ?? null;
		$this->website = $data['artist_website'] ?? $data['website'] ?? null;
		$this->spotify_url = $data['artist_spotify_url'] ?? $data['spotify_url'] ?? null;
		$this->youtube_url = $data['artist_youtube_url'] ?? $data['youtube_url'] ?? null;
		$this->soundcloud_url = $data['artist_soundcloud_url'] ?? $data['soundcloud_url'] ?? null;
		$this->featured_quote = $data['artist_featured_quote'] ?? $data['featured_quote'] ?? null;
		$this->press_quote = $data['artist_press_quote'] ?? $data['press_quote'] ?? null;
		$this->collaborations = $data['artist_collaborations'] ?? $data['collaborations'] ?? null;

		$deletedAt = $data['artist_deleted_at'] ?? $data['deleted_at'] ?? null;
		if ($deletedAt) {
			$this->deleted_at = new DateTime($deletedAt);
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
	/**
	 * Safely reads an optional string field from a data array.
	 * Returns null if the key is missing or empty, otherwise returns the trimmed string.
	 * Prevents storing empty strings in the database for optional fields.
	 */
	private static function optionalTrimmedValue(array $data, string $key): ?string
	{
		if (empty($data[$key])) {
			return null;
		}

		return trim((string)$data[$key]);
	}

	private static function generateSlug(string $text): string
	{
		$text = strtolower($text);
		$text = preg_replace('/[^a-z0-9]+/', '-', $text);

		return trim($text ?? '', '-');
	}
}
