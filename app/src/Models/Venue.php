<?php
namespace App\Models;
use App\Models\EventCategory;

class Venue
{
    public ?int $venue_id = null;
    public string $name = '';
    public string $street_address = '';
    public ?string $postal_code = null;
    public string $city = '';
    public ?string $country = null;
    public ?string $description_html = null;
    public ?int $capacity = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?Media $venue_image = null;
    public ?EventCategory $event_category = null;

    /** Empty constructor; properties are populated via fromPDOData() or createFromPostData(). */
    public function __construct(){}

    /**
     * Factory method that creates a new Venue from raw form POST data.
     * Instantiates a blank Venue and delegates all field assignment to applyPostData(),
     * so this is the right entry point when handling a "create venue" form submission.
     *
     * @param array $data  The raw $_POST array from the venue create form.
     *
     * @return self  A populated Venue instance ready to be passed to the repository.
     */
    public static function createFromPostData(array $data): self
    {
        $venue = new self();
        $venue->applyPostData($data);

        return $venue;
    }

    /**
     * Maps incoming form data onto this Venue instance.
     * Trims all string inputs, defaults city to 'Haarlem' and country to 'NL' if omitted,
     * and silently nulls optional fields (postal code, phone, email, description) that were
     * left blank to avoid storing empty strings in the database.
     *
     * @param array $data  The raw $_POST array from the venue create/edit form.
     *
     * @return void
     */
    public function applyPostData(array $data): void
    {
        $this->name = trim($data['name']);
        $this->street_address = trim($data['street_address']);
        $this->city = trim($data['city'] ?? 'Haarlem');
        $this->postal_code = self::optionalTrimmedValue($data, 'postal_code');
        $this->country = self::optionalTrimmedValue($data, 'country') ?? 'NL';
        $this->capacity = isset($data['capacity']) && $data['capacity'] !== '' ? (int)$data['capacity'] : null;
        $this->phone = self::optionalTrimmedValue($data, 'phone');
        $this->email = self::optionalTrimmedValue($data, 'email');
        $this->description_html = self::optionalTrimmedValue($data, 'description_html');
        $this->latitude = isset($data['latitude']) && $data['latitude'] !== '' ? (float)$data['latitude'] : null;
        $this->longitude = isset($data['longitude']) && $data['longitude'] !== '' ? (float)$data['longitude'] : null;
    }

    /**
     * Builds a comma-separated address string suitable for display in the UI or on maps.
     * Skips any parts (street, postal code, city) that happen to be empty.
     *
     * @return string  E.g. 'Grote Markt 1, 2011 RD, Haarlem'.
     */
    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->street_address,
            $this->postal_code,
            $this->city,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Returns the web-accessible path to the venue's image with a guaranteed leading slash.
     * Falls back to a generic placeholder image when no media record is attached.
     *
     * @return string  A root-relative image path, e.g. '/uploads/venues/grote-markt.jpg'.
     */
    public function getImagePath(): string
    {
        if ($this->venue_image && $this->venue_image->file_path) {
            $path = $this->venue_image->file_path;
            return str_starts_with($path, '/') ? $path : '/' . $path;
        }

        return '/Assets/Home/ImagePlaceholder.png';
    }

    /**
     * Returns the alt text for the venue image, falling back to the venue name.
     * Used for the HTML img alt attribute in templates.
     *
     * @return string  A descriptive alt string, e.g. 'Philharmonie venue'.
     */
    public function getImageAlt(): string
    {
        return $this->venue_image?->alt_text ?? $this->name ?? 'Venue';
    }

    /**
     * Returns true if this venue has a Media record with a non-empty file path.
     * Use this in templates to decide whether to show a real image or a fallback.
     *
     * @return bool  True if a venue image is set, false otherwise.
     */
    public function hasImage(): bool
    {
        return $this->venue_image !== null && !empty($this->venue_image->file_path);
    }

    /**
     * Returns a human-readable capacity label for the venue.
     * Shows 'Open Air' for uncapped venues, or a formatted number such as '1,500 capacity'.
     *
     * @return string  E.g. '500 capacity' or 'Open Air'.
     */
    public function getCapacityDisplay(): string
    {
        if (!$this->capacity || $this->capacity === 0) {
            return 'Open Air';
        }

        return number_format($this->capacity) . ' capacity';
    }

    /**
     * Builds a Google Maps search URL for this venue's address.
     * Useful for linking directly to the map from event detail pages.
     *
     * @return string  A fully formed Google Maps search URL with the address pre-filled.
     */
    public function getMapLink(): string
    {
        $address = urlencode($this->getFullAddress());
        return "https://www.google.com/maps/search/?api=1&query={$address}";
    }

    /**
     * Hydrates this Venue from a raw database row.
     * Handles aliased column names that appear when the VENUE table is JOINed in larger
     * schedule/event queries (e.g. 'venue_name' vs 'name'). Optionally attaches a Media
     * (venue image) and an EventCategory if the relevant columns are present in the row.
     *
     * @param array $data  A single row from PDO::FETCH_ASSOC, potentially with aliased columns.
     *
     * @return void
     */
    public function fromPDOData(array $data): void
{
    $this->venue_id = isset($data['venue_id']) ? (int)$data['venue_id'] : null;
    $this->name = $data['venue_name'] ?? ($data['name'] ?? '');
    $this->street_address = $data['venue_address'] ?? ($data['street_address'] ?? '');
    $this->postal_code = $data['venue_postal_code'] ?? ($data['postal_code'] ?? null);
    $this->city = $data['venue_city'] ?? ($data['city'] ?? '');
    $this->country = $data['venue_country'] ?? ($data['country'] ?? null);
    $this->description_html = $data['venue_description_html'] ?? ($data['description_html'] ?? null);
    $this->capacity = isset($data['venue_capacity']) ? (int)$data['venue_capacity'] : (isset($data['capacity']) ? (int)$data['capacity'] : null);
    $this->phone = $data['venue_phone'] ?? ($data['phone'] ?? null);
    $this->email = $data['venue_email'] ?? ($data['email'] ?? null);
    $this->latitude = isset($data['latitude']) ? (float)$data['latitude'] : null;
    $this->longitude = isset($data['longitude']) ? (float)$data['longitude'] : null;

    $mediaId = $data['venue_image_id'] ?? $data['media_id'] ?? $data['venue_media_id'] ?? null;
    $filePath = $data['image_path'] ?? $data['file_path'] ?? $data['venue_media_file_path'] ?? null;
    $altText = $data['image_alt'] ?? $data['alt_text'] ?? $data['venue_media_alt_text'] ?? null;

    if ($mediaId && $filePath) {
        $this->venue_image = new Media();
        $this->venue_image->fromPDOData([
            'media_id' => $mediaId,
            'file_path' => $filePath,
            'alt_text' => $altText,
        ]);
    }
    if (isset($data['event_id']) || isset($data['event_category_id'])) {
        $this->event_category = new EventCategory();
        $this->event_category->fromPDOData([
            'event_category_id' => $data['event_category_id'] ?? $data['event_id'] ?? null,
            'event_category_title' => $data['event_category_title'] ?? null,
            'event_category_type' => $data['event_category_type'] ?? null,
            'event_category_slug' => $data['event_category_slug'] ?? null,
        ]);
    }
}

    /**
     * Safely extracts a trimmed string from a data array.
     * Returns null when the key is absent or the value is empty, preventing empty
     * strings from being stored in optional database columns.
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
}
