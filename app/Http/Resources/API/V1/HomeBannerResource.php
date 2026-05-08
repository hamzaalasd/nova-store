<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeBannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'subtitle_ar' => $this->subtitle_ar,
            'subtitle_en' => $this->subtitle_en,
            'badge_ar' => $this->badge_ar,
            'badge_en' => $this->badge_en,
            'button_label_ar' => $this->button_label_ar,
            'button_label_en' => $this->button_label_en,
            'image_path' => $this->image_path,
            'background_color' => $this->background_color,
            'accent_color' => $this->accent_color,
            'show_text_overlay' => $this->show_text_overlay,
            'link_type' => $this->link_type,
            'link_value' => $this->link_value,
            'sort_order' => $this->sort_order,
        ];
    }
}
