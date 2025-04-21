<?php

namespace DantSu\OpenStreetMapStaticAPI;

use DantSu\PHPImageEditor\Image;

/**
 * DantSu\OpenStreetMapStaticAPI\TileLayer define tile server url and related configuration
 *
 * @package DantSu\OpenStreetMapStaticAPI
 * @author Stephan Strate <hello@stephan.codes>
 * @access public
 * @see https://github.com/DantSu/php-osm-static-api Github page of this project
 */
class TileLayer
{

    /**
     * Default tile server. OpenStreetMaps with related attribution text
     * @return TileLayer default tile server
     */
    public static function defaultTileLayer(): TileLayer
    {
        return new TileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', '© OpenStreetMap contributors');
    }

    /**
     * @var string Tile server url, defaults to OpenStreetMap tile server
     */
    protected $url;

    /**
     * @var string Tile server attribution according to license
     */
    protected $attributionText;

    /**
     * @var string[] Tile server subdomains
     */
    protected $subdomains;

    /**
     * @var float Opacity
     */
    protected $opacity = 1;

    /**
     * TileLayer constructor
     * @param string $url tile server url with placeholders (`x`, `y`, `z`, `r`, `s`)
     * @param string $attributionText tile server attribution text
     * @param string $subdomains tile server subdomains
     */
    public function __construct(string $url, string $attributionText, string $subdomains = 'abc')
    {
        $this->url = $url;
        $this->attributionText = $attributionText;
        $this->subdomains = \str_split($subdomains);
    }

    /**
     * Set opacity of the layer
     * @param float $opacity Opacity value (0 to 1)
     * @return $this Fluent interface
     */
    public function setOpacity(float $opacity)
    {
        $this->opacity = $opacity;
        return $this;
    }

    /**
     * Get tile url for coordinates and zoom level
     * @param int $x x coordinate
     * @param int $y y coordinate
     * @param int $z zoom level
     * @return string tile url
     */
    public function getTileUrl(int $x, int $y, int $z): string
    {
        return \str_replace(
            ['{r}', '{s}', '{x}', '{y}', '{z}'],
            ['', $this->getSubdomain($x, $y), $x, $y, $z],
            $this->url
        );
    }

    /**
     * Select subdomain of tile server to prevent rate limiting on remote server
     * @param int $x x coordinate
     * @param int $y y coordinate
     * @return string selected subdomain
     * @see https://github.com/Leaflet/Leaflet/blob/main/src/layer/tile/TileLayer.js#L233 Leaflet implementation
     */
    protected function getSubdomain(int $x, int $y): string
    {
        return $this->subdomains[\abs($x + $y) % \sizeof($this->subdomains)];
    }

    /**
     * Get attribution text
     * @return string Attribution text
     */
    public function getAttributionText(): string
    {
        return $this->attributionText;
    }

    /**
     * Get an image tile
     * @return Image Image instance containing the tile
     */
    public function getTile(float $x, float $y, int $z): Image
    {
        if($this->opacity == 0) {
            return Image::newCanvas(256, 256);
        }

        $tile = Image::fromCurl($this->getTileUrl($x, $y, $z));

        if($this->opacity > 0 && $this->opacity < 1) {
            $tile->setOpacity($this->opacity);
        }

        return $tile;
    }
}
