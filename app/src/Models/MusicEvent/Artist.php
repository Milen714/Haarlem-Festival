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

	/** Empty constructor; all properties are populated via fromPDOData() or createFromPostData(). */
	public function __construct() {}

	/**
	 * Factory method that creates a new Artist from raw form POST data.
	 * Instantiates a blank Artist and delegates all field mapping to applyPostData(),
	 * so this is the right entry point when handling a "create artist" form submission.
	 *
	 * @param array $data  The raw $_POST array from the artist create form.
	 *
	 * @return self  A fully populated Artist instance ready to be passed to the repository.
	 */
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
	 *
	 * @param array $data  The raw $_POST array from the artist create/edit form.
	 *
	 * @return void
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

	/**
	 * Returns the web-accessible path to the artist's profile image.
	 * Ensures the path always starts with a leading slash so it works as an absolute URL.
	 * Falls back to a generic placeholder image if no media record is attached.
	 *
	 * @return string  A root-relative image path, e.g. '/uploads/artists/john.jpg'.
	 */
	public function getProfileImagePath(): string
	{
		if ($this->profile_image && $this->profile_image->file_path) {
			$path = $this->profile_image->file_path;
			return str_starts_with($path, '/') ? $path : '/' . $path;
		}

		return '/Assets/Home/ImagePlaceholder.png';
	}

	/**
	 * Returns the alt text for the artist's profile image.
	 * Falls back to the artist's name if no alt text is stored, and to 'Artist' as a last resort.
	 *
	 * @return string  A descriptive alt string suitable for the HTML img alt attribute.
	 */
	public function getProfileImageAlt(): string
	{
		return $this->profile_image?->alt_text ?? $this->name ?? 'Artist';
	}

	/**
	 * Returns the first letter of the artist's name in uppercase.
	 * Used as a visual placeholder (avatar initial) when no profile image is available.
	 * Defaults to 'A' if the name is not set.
	 *
	 * @return string  A single uppercase letter, e.g. 'M' for 'Miles Davis'.
	 */
	public function getInitial(): string
	{
		return strtoupper(substr($this->name ?? 'A', 0, 1));
	}

	/**
	 * Returns true if this artist has a Media record with a non-empty file path attached.
	 * Use this before calling getProfileImagePath() to decide whether to show a real image
	 * or fall back to an initial/avatar placeholder in templates.
	 *
	 * @return bool  True if a profile image is set, false otherwise.
	 */
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
	 *
	 * @param array $data  A single row from PDO::FETCH_ASSOC, potentially with aliased columns.
	 *
	 * @return void
	 */
	public function fromPDOData(array $data): void
	{
		$this->artist_id = isset($data['artist_id']) ? (int)$data['artist_id'] : null;
		$this->name = $data['artist_name'] ?? $data['name'] ?? null;
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
	 *
	 * @param array  $data  The source array (typically $_POST or a PDO row).
	 * @param string $key   The key to look up in the array.
	 *
	 * @return string|null  The trimmed value, or null if blank/missing.
	 */
	private static function optionalTrimmedValue(array $data, string $key): ?string
	{
		if (empty($data[$key])) {
			return null;
		}

		return trim((string)$data[$key]);
	}

	/**
	 * Converts a plain-text name into a URL-safe slug (lowercase, hyphens instead of
	 * spaces and special characters). Used automatically when saving an artist so the
	 * slug stays in sync with the name without any manual input.
	 *
	 * @param string $text  The raw artist name, e.g. 'Chet Baker'.
	 *
	 * @return string  A lowercase hyphenated slug, e.g. 'chet-baker'.
	 */
	private static function generateSlug(string $text): string
	{
		$text = strtolower($text);
		$text = preg_replace('/[^a-z0-9]+/', '-', $text);

		return trim($text ?? '', '-');
	}
}
