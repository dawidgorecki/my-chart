## class.chart.php

With this library you can create a bar chart in PHP. It use GD library to create chart image.  

### Usage

```php
use myChart\myChart;
$chart = new myChart('Chart Title', 600, 400);

$chart->setMargins([70, 30, 50, 30]);
$chart->setColumnPadding(30);
$chart->setTitleSize(16);
$chart->setFontSize(10);
$chart->setBackgroundColor(255,255,255);
$chart->setForegroundColor(0,0,0);
$chart->setColumnColor(66, 139, 202);
$chart->setLineColor(238, 238, 238);
$chart->add('Option 1', 29);
$chart->add('Option 2', 6);
$chart->add('Option 3', 19);
$chart->add('Option 4', 43);
$chart->add('Option 5', 4);

$chart->renderChart(true, true);
```

### Example

![alt text](https://github.com/dawidgorecki/my-chart/blob/master/screen.png)
