<?php
const GGL_CPT__WYSIWYG_OPTIONS = [
	'teeny'         => false,
	'media_buttons' => false,
	"quicktags"     => false,
	"textarea_rows"  => 10,
	"wpautop"        => false,
	"tinymce"       => [
		"toolbar1"       => "bold,italic,underline,blockquote,strikethrough,bullist,numlist,alignleft,aligncenter,alignright,removeformat,undo,redo",
		"toolbar2"       => "",
		"valid_elements" => "strong/b,p[style],em,ul,ol,li,blockquote,del,br,span[style]",
		"invalid_styles" => "display position float clear z-index top right bottom left width height box-sizing overflow clip-path visibility margin margin-top margin-right margin-bottom margin-left padding padding-top padding-right padding-bottom padding-left border border-width border-style border-color border-radius border-top border-bottom border-right border-left outline outline-width outline-style outline-color background background-color background-image background-position background-size background-repeat background-clip background-origin background-attachment background-blend-mode backdrop-filter font-family font-size font-style font-weight font-variant color text-transform letter-spacing line-height word-spacing text-shadow white-space direction list-style-type list-style-position list-style-image flex flex-direction flex-wrap flex-flow flex-grow flex-shrink flex-basis justify-content align-items align-self align-content order grid grid-template-columns grid-template-rows grid-area grid-column grid-row grid-gap transition transition-property transition-duration transition-timing-function transition-delay animation animation-name animation-duration animation-timing-function animation-delay animation-iteration-count animation-direction animation-fill-mode animation-play-state filter box-shadow opacity mix-blend-mode isolation will-change transform transform-origin transform-style perspective perspective-origin backface-visibility border-collapse border-spacing table-layout caption-side empty-cells vertical-align cursor pointer-events resize user-select content quotes counter-set counter-increment counter-reset all"
	]
];

const GGL_COMPATIBLE_POST_TYPES    = [ "movie", "event", "team-member", "supporter" ];
const GGL_GERMAN_DATETIME_FORMAT   = "d.m.Y | H:i";
const GGL_GERMAN_DATE_FORMAT       = "d.m.Y";
const GGL_ENGLISH_DATETIME_FORMAT  = "m/d/Y | g:i a";
const GGL_ENGLISH_DATE_FORMAT      = "m/d/Y";
const GGL_FALLBACK_DATETIME_FORMAT = "Y-m-d H:i";
const GGL_FALLBACK_DATE_FORMAT     = "Y-m-d";