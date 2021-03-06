<?php

namespace maksclub\parser\services;

use maksclub\parser\entities\Video;
use maksclub\parser\forms\VideoForm;
use maksclub\parser\repositories\VideoRepository;
use RicardoFiorani\Matcher\VideoServiceMatcher;


class VideoManageService
{
    /**
     * @var VideoRepository
     */
    private $video;

    /**
     * @var VideoServiceMatcher
     */
    private $matcher;

    /**
     * VideoManageService constructor.
     * @param VideoRepository $video
     * @param VideoServiceMatcher $matcher
     */
    public function __construct(VideoRepository $video, VideoServiceMatcher $matcher)
    {
        $this->video = $video;
        $this->matcher = $matcher;
    }

    /**
     * @param VideoForm $form
     * @return Video
     */
    public function create(VideoForm $form): Video
    {

        /*
         * Match video by URL in form
         */
        $match = $this->matcher->parse($form->url);


        /*
         * No description to youtube
         */
        if($match->getServiceName() == 'Youtube'){
            $description = '';
        }else{
            $description = $match->getDescription();
        }


        /*
         * Create video fields
         */
        $video = Video::create(
            $form->title = $match->getTitle(),
            $form->service = $match->getServiceName(),
            $form->video_id = $match->videoId,
            $form->body = $description,
            $form->embed_code = $match->getEmbedCode(400, 300),
            $form->url = $match->rawUrl,
            $form->large_thumbnail =$match->getLargeThumbnail()
        );

        $this->video->save($video);
        return $video;
    }

}