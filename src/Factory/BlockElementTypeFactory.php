<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\BlockElementType;
use HouseOfAgile\NakaCMSBundle\Repository\BlockElementTypeRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<BlockElementType>
 *
 * @method static BlockElementType|Proxy createOne(array $attributes = [])
 * @method static BlockElementType[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static BlockElementType|Proxy find(object|array|mixed $criteria)
 * @method static BlockElementType|Proxy findOrCreate(array $attributes)
 * @method static BlockElementType|Proxy first(string $sortedField = 'id')
 * @method static BlockElementType|Proxy last(string $sortedField = 'id')
 * @method static BlockElementType|Proxy random(array $attributes = [])
 * @method static BlockElementType|Proxy randomOrCreate(array $attributes = [])
 * @method static BlockElementType[]|Proxy[] all()
 * @method static BlockElementType[]|Proxy[] findBy(array $attributes)
 * @method static BlockElementType[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static BlockElementType[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static BlockElementTypeRepository|RepositoryProxy repository()
 * @method BlockElementType|Proxy create(array|callable $attributes = [])
 */
final class BlockElementTypeFactory extends ModelFactory
{

    protected $blockElements = [
        'carousel' => [
            'htmlCode' => '<div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img src="%% \'picture-1\' %%" class="d-block w-100" alt="...">
              </div>
              <div class="carousel-item">
                <img src="%% \'picture-2\' %%" class="d-block w-100" alt="...">
              </div>
              <div class="carousel-item">
                <img src="%% \'picture-3\' %%" class="d-block w-100" alt="...">
              </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>',
            'jsCode' => '',
            'cssCode' => '',
        ],

        'flex-image-right' => [
            'htmlCode' => '
            <section id="{{ \'genid\' }}">
            <div class="fluid-container">
                <div class="row g-0">
                    <div class="col-md-4 offset-1 d-flex align-items-center">
                        <div class="menu-text flex-grow-1 py-4 px-1">
                            <h2 class="main-title text-left">{{ \'this-element.title\' }}</h2>
                            <div class="row g-0">
                                <div class="col-md-6">
                                    <span class="align-top">{{ \'this-element.size\' }}</span>
                                </div>
                                <div class="col-md-6">
                                    <span class="align-top"> {{ \'this-element.detail\' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="menu-image h-100 d-flex align-items-start">
                            <img src="%% \'picture-49\' %%" class="img-fluid" alt="menu image">
                        </div>`
                    </div>
                </div>
            </div>
        </section>',
            'jsCode' => '',
            'cssCode' => '',
        ],

        'flex-image-left' => [
            'htmlCode' => '<section id="{{ \'genid\' }}">
            <div class="fluid-container">
                <div class="row no-gutters">
                    <div class="col-md">
                        <div class="menu-image h-100 d-flex align-items-start">
                            <img src="%% \'picture-3\' %%" class="img-fluid" alt="menu image">
                        </div>
                    </div>
        
                    <div class="col-md">
                        <div class="menu-text flex-grow-1 py-4 px-5">
                            <h2 class="main-title text-left">{{ \'block-element.title\' }}</h2>
                            <hr class="hr-style-left" />
                            <div class="menu-content d-flex justify-content-between">
                                {{ \'block-element.text\' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>',
            'jsCode' => '',
            'cssCode' => '',
        ],
        'video-full-width' => [
            'htmlCode' => '
                <section id="{{ \'genid\' }}" style="height: 640px;" class="w-100">
                    <div data-youtube="https://www.youtube.com/watch?v=szEMfxAkZbo"></div>
                </section>
        ',
            'jsCode' => '<script type="text/javascript">
    
            $(document).ready(function () {
        $(\'[data-youtube]\').youtube_background({\'fit-box\': true});
        });
        </script>',
            'cssCode' => '<style>
            /* optional css fade in animation */
            iframe {
                height:640px;
                transition: opacity 500ms ease-in-out;
                transition-delay: 250ms;
            }
        </style>',
        ],
        'large-banner' => [
            'htmlCode' => '    <section id="{{ \'genid\' }}" style="height: 400px;">
            <div class="row h-100">
                <div class="col-md-8 offset-2 d-flex align-items-center justify-content-center ">
                    <div class="menu-text">
                        <h2 class="fs-1 animated fadeIn">{{ \'large-banner.title\' }}</h2>
                    </div>
                </div>
            </div>
        </section>',
            'jsCode' => '',
            'cssCode' => '',
        ]
    ];

    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getRamdomBlockElement()
    {
        return array_rand($this->blockElements);
    }


    protected function getDefaults(): array
    {
        $randomBlockElement = $this->getRamdomBlockElement();
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'type' => $randomBlockElement,
            'htmlCode' => $this->blockElements[$randomBlockElement]['htmlCode'],
            'cssCode' => $this->blockElements[$randomBlockElement]['cssCode'],
            'jsCode' => $this->blockElements[$randomBlockElement]['jsCode'],
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(BlockElementType $blockElementType) {})
        ;
    }

    protected static function getClass(): string
    {
        return BlockElementType::class;
    }
}
