<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\SeasonDataTransformerInterface;

class AllSeasonsQueryHandler
{
    private $seasonViewRepository;
    private $seasonViewTransformer;

    public function __construct(
        SeasonViewRepositoryInterface $seasonViewRepository,
        SeasonDataTransformerInterface $seasonViewTransformer
    ) {
        $this->seasonViewRepository = $seasonViewRepository;
        $this->seasonViewTransformer = $seasonViewTransformer;
    }

    public function handle()
    {
        $seasonViews = $this->seasonViewRepository->all();

        $seasons = [];

        foreach ($seasonViews as $seasonView) {
            $seasons[] = $this->seasonViewTransformer->read($seasonView);
        }

        return $seasons;
    }
}
