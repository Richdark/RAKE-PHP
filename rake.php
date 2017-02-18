<?php

/**
 * PHP implementation of Rapid Automatic Keyword Exraction algorithm (RAKE) for extracting multi-word phrases from text.
 *
 * As described in:
 * ROSE, Stuart, et al. Automatic keyword extraction from individual documents. Text Mining, 2010, 1-20.
 *
 * With help of Python implementation - <a href="https://github.com/aneesha/RAKE">github.com/aneesha/RAKE</a>
 *
 * @author Richard Filipčík <richard@filipcik.sk>
 * @version 0.1
 */

class Rake
{
	/**
	 * @var string $stopwords_path
	 */
	public $stopwords_path;

	/**
	 * @var string $stopwords_pattern
	 */
	private $stopwords_pattern;

	/**
	 * Build stop words pattern from file given by parameter
	 *
	 * @param string $stopwords_path Path to the file with stop words
	 */
	function __construct($stopwords_path)
	{
		$this->stopwords_path = $stopwords_path;
		$this->stopwords_pattern = $this->build_stopwords_regex();
	}

	/**
	 * Extract key phrases from input text
	 *
	 * @param string $text Input text
	 */
	public function extract($text)
	{
		$phrases_plain = self::split_sentences($text);
		$phrases = $this->get_phrases($phrases_plain);
		$scores = $this->get_scores($phrases);
		$keywords = $this->get_keywords($phrases, $scores);
		arsort($keywords);

		return $keywords;
	}

	/**
	 * @param string $text Text to be splitted into sentences
	 */
	public static function split_sentences($text)
	{
		return preg_split('/[.?!,;\-"\'\(\)\\\X{2018}\X{2019}\X{2013}\t]+/u', $text);
	}

	/**
	 * @param string $phrase Phrase to be splitted into words
	 */
	public static function split_phrase($phrase)
	{
		$words_temp = str_word_count($phrase, 1, '0123456789');
		$words = array();

		foreach ($words_temp as $w)
		{
			if ($w != '' and !(is_numeric($w)))
			{
				array_push($words, $w);
			}
		}

		return $words;
	}

	/**
	 * Split sentences into phrases by loaded stop words
	 *
	 * @param array $sentences Array of sentences
	 */
	private function get_phrases($sentences)
	{
		$phrases_arr = array();

		foreach ($sentences as $s)
		{
			$phrases_temp = preg_replace($this->stopwords_pattern, '|', $s);
			$phrases = explode('|', $phrases_temp);

			foreach ($phrases as $p)
			{
				$p = strtolower(trim($p));

				if ($p != '') array_push($phrases_arr, $p);
			}
		}

		return $phrases_arr;
	}

	/**
	 * Calculate score for each word
	 *
	 * @param array $phrases Array containing individual phrases
	 */
	private function get_scores($phrases)
	{
		$frequencies = array();
		$degrees = array();

		foreach ($phrases as $p)
		{
			$words = self::split_phrase($p);
			$words_count = count($words);
			$words_degree = $words_count - 1;

			foreach ($words as $w)
			{
				$frequencies[$w] = (isset($frequencies[$w]))? $frequencies[$w] : 0;
				$frequencies[$w] += 1;
				$degrees[$w] = (isset($degrees[$w]))? $degrees[$w] : 0;
				$degrees[$w] += $words_degree;
			}
		}

		foreach ($frequencies as $word => $freq)
		{
			$degrees[$word] += $freq;
		}

		$scores = array();

		foreach ($frequencies as $word => $freq)
		{
			$scores[$word] = (isset($scores[$word]))? $scores[$word] : 0;
			$scores[$word] = $degrees[$word] / (float) $freq;
		}

		return $scores;
	}

	/**
	 * Calculate score for each phrase by words scores
	 *
	 * @param array $phrases Array of phrases (optimally) returned by get_phrases() method
	 * @param array $scores Array of words and their scores returned by get_scores() method
	 */
	private function get_keywords($phrases, $scores)
	{
		$keywords = array();

		foreach ($phrases as $p)
		{
			$keywords[$p] = (isset($keywords[$p]))? $keywords[$p] : 0;
			$words = self::split_phrase($p);
			$score = 0;

			foreach ($words as $w)
			{
				$score += $scores[$w];
			}

			$keywords[$p] = $score;
		}

		return $keywords;
	}

	/**
	 * Get loaded stop words and return regex containing each stop word
	 */
	private function build_stopwords_regex()
	{
		$stopwords_arr = $this->load_stopwords();
		$stopwords_regex_arr = array();

		foreach ($stopwords_arr as $word)
		{
			array_push($stopwords_regex_arr, '\b'. $word. '\b');
		}

		return '/'. implode('|', $stopwords_regex_arr). '/i';
	}

	/**
	 * Load stop words from an input file
	 */
	private function load_stopwords()
	{
		$stopwords = array();

		if ($h = @fopen($this->stopwords_path, 'r'))
		{
			while (($line = fgets($h)) !== false)
			{
				$line = trim($line);

				if ($line[0] != '#')
				{
					array_push($stopwords, $line);
				}
			}

			return $stopwords;
		}
		else
		{
			echo 'Error: could not read file "'. $this->stopwords_path. '".';

			return false;
		}
	}
}

?>