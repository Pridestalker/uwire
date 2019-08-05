<?php
namespace App\Controllers\Customizer\General;

use Kirki;
use DusanKasan\Knapsack\Collection;

/**
 * Class Customizer
 *
 * @package App\Controllers\Customizer
 */
abstract class Customizer
{
    
    /**
     * Holds the Kirki instance.
     *
     * @var Kirki $kirki
     */
    private $kirki;
    
    /**
     * This holds the section data.
     *
     * @var array Holds the section data.
     */
    protected $section;
    
    /**
     * The current section name.
     *
     * @var string $section_name
     */
    protected $section_name;
    
    /**
     * The section priority
     *
     * @var int $section_priority
     */
    protected $section_priority = 160;
    
    /**
     * This holds the panel data.
     *
     * @var array Holds the panel data.
     */
    protected $panel;
    
    /**
     * The panel name.
     *
     * @var string|null
     */
    protected $panel_name = null;
    
    /**
     * The panel name.
     *
     * @var integer
     */
    protected $panel_priority = 160;
    
    /**
     * Holds the fields
     *
     * @var array
     */
    protected $fields;
    
    /**
     * Customizer constructor.
     */
    public function __construct()
    {
        $this->register_panel();
        $this->register_sections();
        $this->register_fields();
        
        $this->register_custom_controls();
    }
    
    /**
     * Register a new panel
     *
     * @return mixed
     */
    private function register_panel(): void
    {
        if (null !== $this->panel) {
            Kirki::add_panel(
                $this->panel_name,
                [
                    'priority'    => $this->panel_priority,
                    'title'       => $this->panel['title'],
                    'description' => $this->panel['description'],
                ]
            );
        }
    }
    
    /**
     * Register a new section
     *
     * @return void
     */
    private function register_sections(): void
    {
        Kirki::add_section(
            $this->section_name,
            [
                'title'       => $this->section['title'],
                'description' => $this->section['description'],
                'panel'       => $this->panel_name,
                'priority'    => $this->section_priority,
            ]
        );
    }
    
    /**
     * Register all fields
     *
     * @return void
     */
    private function register_fields(): void
    {
        foreach ($this->fields as $field) {
            $field = new Collection($field);
            
            Kirki::add_field(
                $field->get('id'),
                $field->only([ 'type', 'settings', 'label', 'section', 'default', 'priority', 'description', 'choices' ])->toArray()
            );
        }
    }
    
    /**
     * Register custom fields
     *
     * @return void
     */
    public function register_custom_controls(): void
    {
    }
}
