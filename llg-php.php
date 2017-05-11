<?php
declare(strict_types=1);

function main()
{
    $dic = array_filter(array_map('trim', preg_split('~[\n\r]+~', file_get_contents('php://stdin'))));

    $pathFinder = new PathFinder();
    $result = $pathFinder->findLongest($dic);

    printf('[%s]', implode(' ', $result));
}

main();

class PathFinder
{
    /**
     * @var []string
     */
    private $dic;

    /**
     * @var []string
     */
    private $result = [];

    /**
     * @var [][]int
     */
    private $lookup;

    /**
     * @var []bool
     */
    private $visited = [];

    public function findLongest(array $dic): array
    {
        $this->init($dic);

        $this->find(count($dic), []);
        return $this->result;
    }

    private function enter(int $i)
    {
        $this->visited[$i] = true;
    }
    
    private function quit(int $i)
    {
        $this->visited[$i] = false;
    }
    
    private function isVisited(int $i): bool
    {
        return $this->visited[$i];
    }

    private function find($currentIndex, array $rest): array
    {
        foreach ($this->lookup[$currentIndex] as $nextIndex) {
            if ($this->isVisited($nextIndex)) {
                continue;
            }

            $this->enter($nextIndex);

            $candidate = array_merge($rest, [$this->dic[$nextIndex]]);
            $candidate = $this->find($nextIndex, $candidate);

            $this->quit($nextIndex);

            if (count($candidate) > count($this->result)) {
                $this->result = $candidate;
            }
        }

        return $rest;
    }

    private function init(array $dic)
    {
        $this->dic = $dic;

        foreach($dic as $io => $wo) {
            $this->lookup[$io] = [];

            foreach($dic as $ii => $wi) {
                $lastCharacter = $wo[strlen($wo) - 1];
                if ($lastCharacter === $wi[0] && $wo !== $wi) {
                    $this->lookup[$io][] = $ii;
                }
            }
        }

        $this->lookup[count($dic)] = range(0, count($dic) - 1);

        $this->visited = array_fill(0, count($dic), false);
    }
}
