<?php

class Country {
	public string $numerical {
		get => $this->numerical;
	}
	public string $alpha2 {
		get => $this->alpha2;
	}

	public string $german_comment {
		get => $this->german_comment;
	}

	public string $english_comment {
		get => $this->english_comment;
	}

	public string $german_name {
		get => $this->german_name;
	}

	public string $english_name {
		get => $this->english_name;

	}

	public string $official_german_name {
		get => $this->official_german_name;
	}

	public string $official_english_name {
		get => $this->official_english_name;

	}

	public function __construct( $numerical, $alpha2, $german_comment, $english_comment, $german_name, $official_german_name, $english_name, $official_english_name ) {
		$this->numerical             = $numerical;
		$this->alpha2                = $alpha2;
		$this->german_comment        = $german_comment;
		$this->english_comment       = $english_comment;
		$this->german_name           = $german_name;
		$this->english_name          = $english_name;
		$this->official_german_name  = $official_german_name;
		$this->official_english_name = $official_english_name;
	}
}