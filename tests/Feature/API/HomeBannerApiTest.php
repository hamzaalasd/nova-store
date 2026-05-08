<?php

namespace Tests\Feature\API;

use App\Models\HomeBanner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeBannerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_banners_endpoint_returns_visible_banners_in_order(): void
    {
        HomeBanner::create([
            'title_ar' => 'بنر ظاهر ثاني',
            'subtitle_ar' => 'وصف قصير',
            'image_path' => 'home-banners/second.jpg',
            'link_type' => 'products',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        HomeBanner::create([
            'title_ar' => 'بنر ظاهر أول',
            'image_path' => 'home-banners/first.jpg',
            'link_type' => 'category',
            'link_value' => '5',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        HomeBanner::create([
            'title_ar' => 'بنر مخفي',
            'is_active' => false,
            'sort_order' => 0,
        ]);

        $this->getJson('/api/v1/home-banners')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.title_ar', 'بنر ظاهر أول')
            ->assertJsonPath('data.0.link_type', 'category')
            ->assertJsonMissing(['title_ar' => 'بنر مخفي']);
    }
}
