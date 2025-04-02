<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IslamicStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'source',
        'image_url',
        'video_url',
        'category',
        'featured',
        'view_count'
    ];

    public function incrementViewCount()
    {
        $this->view_count++;
        $this->save();
    }

    /**
     * Get the embedded video HTML for the story
     *
     * @return string|null
     */
    public function getEmbeddedVideoHtml()
    {
        if (!$this->video_url) {
            return null;
        }

        // Handle YouTube URLs
        if (strpos($this->video_url, 'youtube.com') !== false || strpos($this->video_url, 'youtu.be') !== false) {
            // Extract YouTube video ID
            $videoId = null;
            if (preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->video_url, $matches)) {
                $videoId = $matches[1];
            }
            if ($videoId) {
                return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe>';
            }
            return null;
            // Handle Vimeo URLs
            } elseif (strpos($this->video_url, 'vimeo.com') !== false) {
            // Extract Vimeo video ID
            $videoId = null;
            if (preg_match('/(?:vimeo\.com\/(?:video\/|channels\/|groups\/|(?:[^\/]+\/)(?:[^\/]+\/))|vimeo\.com\/)(\d+)/', $this->video_url, $matches)) {
                $videoId = $matches[1];
            }
            if ($videoId) {
                return '<iframe src="https://player.vimeo.com/video/' . $videoId . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
            }
            return null;
        } elseif (strpos($this->video_url, 'dailymotion.com') !== false) {
            // Extract Dailymotion video ID
            $videoId = null;
            if (preg_match('/(?:dailymotion\.com\/(?:video|hub)\/|dai\.ly\/)([a-zA-Z0-9]+)/', $this->video_url, $matches)) {
                $videoId = $matches[1];
            }
            if ($videoId) {
                return '<iframe frameborder="0" width="480" height="270" src="https://www.dailymotion.com/embed/video/' . $videoId . '" allowfullscreen></iframe>';
            }
            return null;
        }
        return null;
        // return null if no valid video URL is found
    }
}
