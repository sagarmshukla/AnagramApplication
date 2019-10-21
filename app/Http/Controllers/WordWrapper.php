<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WordWrapper extends Controller
{
    /**
	 * Get last request's parameters
	 * @return array
	 */
	public function getWordWrapped(Request $request)
	{
	        $output = $request->input('word');

		if ($output == null) {
			return $this->generateResponse(0,"value missing for word.",false,200);
		}
		if ( preg_match('/\s/',$output) )
		{
			return $this->generateResponse(0,"Enter single word only.",false,200);
    		
		}

		$len = strlen($output);
		$chars = str_split($output);

		$result = array();
		$outputdata = array();
		$this->permute($output,0, $len, $outputdata);
		//$result[$len] = $outputdata;
		$result[$len] = $this->spellChecker($outputdata);

		for ($currentlen = $len-1 ; $currentlen >= 2 ; $currentlen = $currentlen -1) {
			$output = $this->sampling($chars, $currentlen);
			//$result[$currentlen] = $output;
			$result[$currentlen] = $this->spellchecker($output);
		}
	  	return $this->generateResponse(1,"value provided.",$result,200);
	}

	// function to validate the generated combination for spell check with aspell lib.
	public function spellChecker($dataArray) {
		$finalwords = array();
		$pspell_link = pspell_new("en");
		foreach ($dataArray as $key) {
		  if (pspell_check($pspell_link, $key)) {
		    if (!in_array($key, $finalwords)) {
		      $finalwords[] = $key;
		    }
		  }
		}
		return $finalwords;
	}

	// function to generate response for the class. 
	public function generateResponse($status,$msg,$data = false,$code = 200) {
   		$result = array('status' => $status,'msg' => $msg);
   		if($data != false) {
   			$result['data'] = $data;
   		}
   		return response()->json($result,$code);
   	}

    // Function to generate Permutation Combination for all possible string with same length.
    public function permute($str,$i,$n, &$outputdata) {
   		if ($i == $n)
       		$outputdata[] = $str;
   		else {
        	for ($j = $i; $j < $n; $j++) {
          		$this->swap($str,$i,$j);
          		$this->permute($str, $i+1, $n, $outputdata);
          		$this->swap($str,$i,$j); // backtrack.
       		}
   		}
	}

	// function to swap the char at pos $i and $j of $str.
	public function swap(&$str,$i,$j) {
    	$temp = $str[$i];
    	$str[$i] = $str[$j];
    	$str[$j] = $temp;
	}   	

	public function sampling($chars, $size, $combinations = array()) {

	    # if it's the first iteration, the first set 
	    # of combinations is the same as the set of characters
	    if (empty($combinations)) {
	        $combinations = $chars;
	    }

	    # we're done if we're at size 1
	    if ($size == 1) {
	        return $combinations;
	    }

	    # initialise array to put new values in
	    $new_combinations = array();

	    # loop through existing combinations and character set to create strings
	    foreach ($combinations as $combination) {
	        foreach ($chars as $char) {
	            $new_combinations[] = $combination . $char;
	        }
	    }

	    # call same function again for the next iteration
	    return $this->sampling($chars, $size - 1, $new_combinations);

	}
}
