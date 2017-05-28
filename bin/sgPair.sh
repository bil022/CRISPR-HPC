sgRNA=$1
if ! [ -e "$sgRNA" ]; then sgRNA=sgRNA.mm9.chr14.61000000-66000000.txt; fi

for n in `seq 10 3 40`; do
  cat $sgRNA | ../bin/sgPair.pl $n 2> sgPair.$n.log > /dev/null; head sgPair.$n.log
done
