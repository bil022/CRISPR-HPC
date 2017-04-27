<?php
	ini_set('display_errors', '1');

	$win=200; $step=10;	
	if (isset($_POST['json'])) {
		$json=true;
		$data=json_decode($_POST['json']);
		$ref=$data->{'ref'};
		$chr=$data->{'chr'};
		$s=$data->{'s'};
		$e=$data->{'e'};
		$method=$data->{'method'};
	} elseif (isset($_REQUEST['ref'])) {
		$json=false;
		$ref=$_REQUEST['ref'];
		$chr=$_REQUEST['chr'];
		$s=$_REQUEST['s'];
		$e=$_REQUEST['e'];
		if (isset($_REQUEST['win'])) $win=$_REQUEST['win'];
		if (isset($_REQUEST['step'])) $step=$_REQUEST['step'];
		$method=$_REQUEST['method'];
	} else {
		system("Hi, there!");
		exit(-1);
	}
	
	$samtools="bin/samtools";
	$crest="bin/crest";
	if (PHP_OS=='Darwin') {
		$samtools.=".mac";
		$crest.=".mac";
	}
	
	if ($method=='sgRNA') {
		$script="$samtools view bam/$ref.bam $chr:$s-$e";
		// ecd6fbc7a4:ACTTGCAGGTGGTCCGAGTG	16	chr6	31132633	30	20M	*	0	0	CACTCGGACCACCTGCAAGT	IIIIIIIIIIIIIIIIIIII
		$cols=array('chr', 'start', 'end', 'seq', 'strand');
		// chr6	31132633		31132633	ACTTGCAGGTGGTCCGAGTG	+
		$header=array('chr'=>'Shr', 'start'=>'Start', 'end'=>'End', 'seq'=>'Seq', 'strand'=>'Strand');
		$dotdot=array('chr'=>'...', 'start'=>'', 'end'=>'', 'seq'=>'', 'strand'=>'');
		$parser=function($line) {
			$data = preg_split("/[\s:]+/", $line); $strand='+'; if ($data[2]==16) $strand='-';
			return array('chr'=>$data[3], 'start'=>$data[4], 'end'=>$data[4]+20, 'seq'=>$data[1], 'strand'=>$strand);
		};
		$log="buf/$method.$ref.$chr.$s-$e.log";
		$output="buf/$method.$ref.$chr.$s-$e.txt";
	} else {
		$script="$samtools view bam/$ref.bam $chr:$s-$e | $crest -q $chr:$s-$e -w $win -s $step";
		// chr6	ACTTGCAGGTGGTCCGAGTG	31132635	-	AAACTGAGGATGACTGGGTT	31136477	+	3842 bp
		$cols=array('chr', 'seqA', 'cutA', 'strandA', 'seqB', 'cutB', 'strandB', 'dist');
		$header=array('chr'=>'Chr', 'seqA'=>'SeqA', 'cutA'=>'CutA', 'strandA'=>'StrandA', 'seqB'=>'SeqB', 'cutB'=>'CutB', 'strandB'=>'StrandB', 'dist'=>'Dist');
		$dotdot=array('chr'=>'...', 'cutA'=>'', 'seqA'=>'', 'strandA'=>'', 'cutB'=>'', 'seqB'=>'', 'strandB'=>'', 'dist'=>'');
		$parser=function($line) {
			$data = preg_split("/[\s]+/", $line);
			return array('chr'=>$data[0], 'cutA'=>$data[1], 'seqA'=>$data[2], 'strandA'=>$data[3], 'cutB'=>$data[4], 'seqB'=>$data[5], 'strandB'=>$data[6], 'dist'=>$data[7]);
		};
		$log="buf/$method.$ref.$chr.$s-$e.w$win.s$step.log";
		$output="buf/$method.$ref.$chr.$s-$e.w$win.s$step.txt";
	}
	
	if (!file_exists($output)) {
		system("$script 2> $log > $output");
	}
	
	if ($json) {
		$lines = file($output);
		$result=array(); $result[]=$header; $n=0; $more='';
		foreach ($lines as $line) {
			if ($n>=100) {
				$result[]=$dotdot;
				$more='+';
				break;
			}
			$result[]=$parser($line);
			$n++;
		}
		
		$count=count($result);
		$msg="Found $n$more candidates at $chr:$s-$e from $ref with $method.";
		$ret=array('good'=>true, 'message'=>"$msg", 'script'=>$script, 'cols'=>$cols, 'table'=>$result);
		print json_encode($ret);
	} else {
		header("Content-Type: text/plain");
		include($output);
	}
?>
