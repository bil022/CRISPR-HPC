# CRISPR-web

Whole genome sgRNA design database for human and mouse. Due to the limites of file size, the bam files are uploaded to http://enhancer.sdsc.edu/bli/CREST-web/

To re-generage the database, the whole genome sequence is needed, for example:
####	ref=hg19
####	./crest -f $ref.fa -r $ref
####	cat $ref.h $ref.mm4 | samtools view -Sb - | samtools sort -@ 8 - $ref
####	samtools index $ref.bam
