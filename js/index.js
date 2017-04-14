(function () {
  'use strict';
  angular
      .module('MyApp',['ngMaterial', 'ngMessages', 'material.svgAssetsCache'])
      .controller('AppCtrl', AppCtrl);

  function AppCtrl ($scope, $http, $log) {
    var tabs = [
          { title: 'HOME', html: 'include/home.html' },
          { title: 'Downloads', html: 'include/downloads.html' },
          { title: 'Software', html: 'include/software.html' },
          { title: 'About', html: 'include/about.html' }
        ],
        selected = null,
        previous = null;
    $scope.data = "hello"; 
    $scope.tabs = tabs;
    $scope.selectedIndex = 0;
    $scope.$watch('selectedIndex', function(current, old){
      previous = selected;
      selected = tabs[current];
    });
    $scope.constant={
      refs: [ {id:'mm9'},{id:'mm10'},{id:'hg18'},{id:'hg19'},{id:'hg38'} ],
      hgChrs: [ { id: 'chr1'}, {id: 'chr2'}, {id: 'chr3'}, {id: 'chr4'}, {id: 'chr5'}, {id: 'chr6'}, {id: 'chr7'}, {id: 'chr8'}, {id: 'chr9'}, {id: 'chr10'}, {id: 'chr11'}, {id: 'chr12'}, {id: 'chr13'}, {id: 'chr14'}, {id: 'chr15'}, {id: 'chr16'}, {id: 'chr17'}, {id: 'chr18'}, {id: 'chr19'}, {id: 'chr20'}, {id: 'chr21'}, {id: 'chr22'}, {id: 'chrX'}, {id: 'chrY'} ],
      mmChrs: [ { id: 'chr1'}, {id: 'chr2'}, {id: 'chr3'}, {id: 'chr4'}, {id: 'chr5'}, {id: 'chr6'}, {id: 'chr7'}, {id: 'chr8'}, {id: 'chr9'}, {id: 'chr10'}, {id: 'chr11'}, {id: 'chr12'}, {id: 'chr13'}, {id: 'chr14'}, {id: 'chr15'}, {id: 'chr16'}, {id: 'chr17'}, {id: 'chr18'}, {id: 'chr19'}, {id: 'chrX'}, {id: 'chrY'} ],
      method: [ { id: 'sgRNA'}, { id: 'Tiling'}]
   };
    $scope.data={
      ref: 'hg19',
      chr: 'chr6',
      s: 31132114,
      e: 31238451,
      method: 'sgRNA'
   };
   $scope.help=0;
   $scope.hgChr='chr1';
   $scope.mmChr='chr1';
   $scope.php={
   };
 
   $scope.go4table = function() {
      $scope.help++;
      var strJson=JSON.stringify($scope.data);
      var postData = 'json='+strJson;
      $log.debug(postData);
      $scope.php.table="";
      $scope.php.message='Checking '+strJson;
      $scope.php.err="";
      $http({
        method : 'POST',
        url    : 'table.php',
        data: postData,
        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
      }).success(function(data) {
        $log.debug(data);
        if (data.good) {
          $scope.php.cols=data.cols;
          $scope.php.table=data.table;
          $scope.php.message=data.message;
          $scope.php.script=data.script;
        } else {
          $scope.php.message="";
          $scope.php.err=data;
        }
        //$scope.debug=data;
        //$scope.php.table=data;
        //$scope.php.message=data;
     }).error(function(error) {
        $log.debug(error);
        //$scope.php.message=error;
     });
   }
  }
})();

/**
Copyright 2016 Google Inc. All Rights Reserved. 
Use of this source code is governed by an MIT-style license that can be in foundin the LICENSE file at http://material.angularjs.org/license.
**/
