<?php

namespace myChart;

class myChart
{
    // chart settings
    private $chart;
    private $chartWidth; 
    private $chartHeight; 
    private $chartMargins; // array [top, right, bottom, left]
    private $chartTitle;

    private $font;
    private $fontSize;
    private $titleSize;

    // colors
    private $bgColor; 
    private $fgColor; 
    private $columnColor; 
    private $lineColor;

    // column settings
    private $columnWidth;
    private $columnPadding;

    private $values;
    private $valuesCount;
    private $valuesTotal;

    private $offsetX;
    private $offsetY;

    private $percentage;
    private $step;

    function __construct($title, $width = 640, $height = 480)
    {   
        putenv('GDFONTPATH=' . realpath('.'));

        $this->font = 'OpenSans-Regular.ttf';
        $this->titleSize = 16; 
        $this->fontSize = 10;
        
        $this->chartWidth = $width;
        $this->chartHeight = $height;
        $this->chartMargins = [20, 20, 20, 20];
        $this->chartTitle = $title;
        $this->values = [];
        $this->valuesCount = 0;
        $this->valuesTotal = 0;
        $this->columnWidth = 0;
        $this->columnPadding = 30;
        $this->offsetX = 0;
        $this->offsetY = 0;
        
        $this->chart = imagecreatetruecolor($this->chartWidth, $this->chartHeight);   

        $this->bgColor = imagecolorallocate($this->chart, 255, 255, 255);   
        $this->fgColor = imagecolorallocate($this->chart, 0, 0, 0);   
        $this->columnColor = imagecolorallocate($this->chart, 66, 139, 202);   
        $this->lineColor = imagecolorallocate($this->chart, 238, 238, 238);   
    }

    function __destruct()
    {
        imagedestroy($this->chart); 
    }

    public function add($description, $value) 
    {
        $this->values[] = [$description, $value];
    }

    public function setMargins($margins) 
    {
        $this->chartMargins = $margins;
    }

    public function setTitleSize($size) 
    {
        $this->titleSize = $size;
    }     

    public function setFontSize($size) 
    {
        $this->fontSize = $size;
    } 

    /**
     * define space between columns
     * @param int $padding
     */
    public function setColumnPadding($padding)
    {
        $this->columnPadding = $padding;
    }

    /**
     * set the color of chart background
     * @param int $r red
     * @param int $g green
     * @param int $b blue
     */
    public function setBackgroundColor($r, $g, $b)
    {
        $this->bgColor = imagecolorallocate($this->chart, $r, $g, $b);
    }    

    /**
     * set the color of chart foreground
     * @param int $r red
     * @param int $g green
     * @param int $b blue
     */
    public function setForegroundColor($r, $g, $b)
    {
        $this->fgColor = imagecolorallocate($this->chart, $r, $g, $b);
    }    

    /**
     * set the color of the column
     * @param int $r red
     * @param int $g green
     * @param int $b blue
     */
    public function setColumnColor($r, $g, $b)
    {
        $this->columnColor = imagecolorallocate($this->chart, $r, $g, $b);
    }    

    /**
     * set the color of the line
     * @param int $r red
     * @param int $g green
     * @param int $b blue
     */
    public function setLineColor($r, $g, $b)
    {
        $this->lineColor = imagecolorallocate($this->chart, $r, $g, $b);
    }

    public function renderChart($bg = true, $border = true)
    {
        header('Content-Type: image/png');

        $this->valuesCount = count($this->values);

        foreach ($this->values as $value) {
            $this->valuesTotal += $value[1];
        }

        reset($this->values);

        $this->columnWidth = (($this->chartWidth - $this->chartMargins[1] - $this->chartMargins[3]) -
                              ($this->valuesCount * $this->columnPadding) + $this->columnPadding) / $this->valuesCount;

        $this->offsetX = $this->chartMargins[3];
        $this->offsetY = $this->chartHeight - $this->chartMargins[2];

        if ($bg) imagefill($this->chart, 0, 0, $this->bgColor);
        if ($border) imagerectangle($this->chart, 0, 0, $this->chartWidth - 1, $this->chartHeight - 1, $this->fgColor);

        imageline($this->chart, $this->offsetX - 5, $this->offsetY, 
                    $this->chartWidth - $this->chartMargins[1] + 5, $this->offsetY, $this->fgColor);

        for ($i=1; $i < 11; $i++) { 
            $this->offsetY -= round(($this->chartHeight - $this->chartMargins[0] - $this->chartMargins[2]) / 10);
            imageline($this->chart, $this->offsetX, $this->offsetY, 
                      $this->chartWidth - $this->chartMargins[1], $this->offsetY, $this->lineColor);
        }

        $titleBox = imagettfbbox($this->titleSize, 0, $this->font, $this->chartTitle);
        $titleWidth = $titleBox[2] - $titleBox[0];
        $titleHeight = abs($titleBox[7] - $titleBox[1]);
        $titleX = ($this->chartWidth - $titleWidth) / 2; 
        $titleY = $this->chartMargins[0] - $titleHeight;
    
        imagettftext($this->chart, $this->titleSize, 0, $titleX, $titleY,
             $this->fgColor, $this->font, $this->chartTitle);

        $this->step = round(($this->chartHeight - $this->chartMargins[0] - $this->chartMargins[2]) / 100);

        $total = [];
        $counter = 0;

        foreach ($this->values as $value) {
            $counter++;
            if ($value > 0) {
                $this->percentage = number_format(($value[1] / $this->valuesTotal) * 100, 2);
                
                if ($counter == count($this->values)) {
                    $this->percentage = 100 - array_sum($total);
                } else {
                    $total[] = $this->percentage;
                }   
            } else {
                $this->percentage = 0; 
            } 

            imagefilledrectangle($this->chart, 
                $this->offsetX, 
                $this->chartHeight - $this->chartMargins[2] - 1, 
                $this->offsetX + $this->columnWidth, 
                $this->chartHeight - $this->chartMargins[2] - ($this->percentage * $this->step), 
                $this->columnColor);

            $titleBox = imagettfbbox($this->fontSize, 0, $this->font, $value[0]);
            $titleWidth = $titleBox[2] - $titleBox[0];
            $titleHeight = abs($titleBox[7] - $titleBox[1]);
            $titleX = $this->offsetX + (($this->columnWidth - $titleWidth) / 2); 
            $titleY = $this->chartHeight - $this->chartMargins[2] + $titleHeight + 5;

            imagettftext($this->chart, $this->fontSize, 0, 
                $titleX, 
                $titleY,
             $this->fgColor, $this->font, $value[0]);

            $titleBox = imagettfbbox($this->fontSize, 0, $this->font, $this->percentage . ' %');
            $titleWidth = $titleBox[2] - $titleBox[0];
            $titleHeight = abs($titleBox[7] - $titleBox[1]);
            $titleX = $this->offsetX + (($this->columnWidth - $titleWidth) / 2); 
            $titleY = $this->chartHeight - $this->chartMargins[2] - ($this->percentage * $this->step) - $titleHeight;

            imagettftext($this->chart, $this->fontSize, 0, 
                $titleX, 
                $titleY,
             $this->fgColor, $this->font, $this->percentage . ' %');

            $this->offsetX += $this->columnWidth + $this->columnPadding;
        }

       imagepng($this->chart);
    }
}