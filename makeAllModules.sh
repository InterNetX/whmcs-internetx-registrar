#!/bin/bash
## Registrar Names
declare -a registrars=("InterNetX" "DomainRobot" "SchlundTech")

## Registrar Urls
declare -a registrarurls=("www.internetx.com" "www.domain-robot.org" "www.schlundtech.com")

## Registrar Nameservers
declare -a registrarns=("ns14.net" "ns14.net" "schlundtech.de")

# get length of an array
arraylength=${#registrars[@]}

echo "Op. count: ${arraylength}"

# use for loop to read all values and indexes
for (( i=0; i < ${arraylength}; i++ ));
do
   echo $(($i + 1)) " / " ${arraylength} " : " ${registrars[$i]}
   echo "################### Registrar: ${registrars[$i]} – Url: ${registrarurls[$i]} - NS: ${registrarns[$i]} ################################### "
   ./makeModule.sh ${registrars[$i]} ${registrarurls[$i]} ${registrarns[$i]}
   echo ""
done


# # And Copy SRC AS IS to dist just for the sake of order
# echo "################### InterNetX ################################### "
# echo "Since this is the original, just copying the src folder over to dist/InterNetX"
# rm -rf dist/InterNetX
# cp -a src dist/InterNetX
# echo "done"


