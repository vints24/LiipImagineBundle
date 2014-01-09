<?php

namespace Liip\ImagineBundle\Tests\Filter;

use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use Liip\ImagineBundle\Model\Binary;
use Liip\ImagineBundle\Tests\AbstractTest;

/**
 * @covers Liip\ImagineBundle\Imagine\Filter\FilterManager
 */
class FilterManagerTest extends AbstractTest
{
    public function testThrowsIfNoLoadersAddedForFilterOnApplyFilter()
    {
        $config = $this->getMockFilterConfiguration();
        $config
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('thumbnail')
            ->will($this->returnValue(array(
                'filters' => array(
                    'thumbnail' => array(
                        'size' => array(180, 180),
                        'mode' => 'outbound',
                    ),
                ),
            )))
        ;

        $binary = new Binary('aContent', 'image/png', 'png');

        $filterManager = new FilterManager($config, $this->getMockImagine());

        $this->setExpectedException('InvalidArgumentException', 'Could not find filter loader for "thumbnail" filter type');
        $filterManager->applyFilter($binary, 'thumbnail');
    }

    public function testReturnFilteredBinaryWithExpectedContentOnApplyFilter()
    {
        $originalContent = 'aOriginalContent';
        $expectedFilteredContent = 'theFilteredContent';

        $binary = new Binary($originalContent, 'image/png', 'png');

        $thumbConfig = array(
            'size' => array(180, 180),
            'mode' => 'outbound',
        );

        $config = $this->getMockFilterConfiguration();
        $config
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('thumbnail')
            ->will($this->returnValue(array(
                'filters' => array(
                    'thumbnail' => $thumbConfig,
                ),
            )))
        ;

        $image = $this->getMockImage();
        $image
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($expectedFilteredContent))
        ;

        $imagine = $this->getMockImagine();
        $imagine
            ->expects($this->once())
            ->method('load')
            ->with($originalContent)
            ->will($this->returnValue($image))
        ;

        $loader = $this->getMockLoader();
        $loader
            ->expects($this->once())
            ->method('load')
            ->with($this->identicalTo($image), $thumbConfig)
            ->will($this->returnArgument(0))
        ;

        $filterManager = new FilterManager($config, $imagine);
        $filterManager->addLoader('thumbnail', $loader);

        $filteredBinary = $filterManager->applyFilter($binary, 'thumbnail');

        $this->assertInstanceOf('Liip\ImagineBundle\Model\Binary', $filteredBinary);
        $this->assertEquals($expectedFilteredContent, $filteredBinary->getContent());
    }

    public function testReturnFilteredBinaryWithFormatOfOriginalBinaryOnApplyFilter()
    {
        $originalContent = 'aOriginalContent';
        $expectedFormat = 'theFormat';

        $binary = new Binary($originalContent, 'image/png', $expectedFormat);

        $thumbConfig = array(
            'size' => array(180, 180),
            'mode' => 'outbound',
        );

        $config = $this->getMockFilterConfiguration();
        $config
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('thumbnail')
            ->will($this->returnValue(array(
                'filters' => array(
                    'thumbnail' => $thumbConfig,
                ),
            )))
        ;

        $image = $this->getMockImage();
        $image
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue('aFilteredContent'))
        ;

        $imagine = $this->getMockImagine();
        $imagine
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue($image))
        ;

        $loader = $this->getMockLoader();
        $loader
            ->expects($this->once())
            ->method('load')
            ->with($this->identicalTo($image), $thumbConfig)
            ->will($this->returnArgument(0))
        ;

        $filterManager = new FilterManager($config, $imagine);
        $filterManager->addLoader('thumbnail', $loader);

        $filteredBinary = $filterManager->applyFilter($binary, 'thumbnail');

        $this->assertInstanceOf('Liip\ImagineBundle\Model\Binary', $filteredBinary);
        $this->assertEquals($expectedFormat, $filteredBinary->getFormat());
    }

    public function testReturnFilteredBinaryWithMimeTypeOfOriginalBinaryOnApplyFilter()
    {
        $originalContent = 'aOriginalContent';
        $expectedMimeType = 'theFormat';

        $binary = new Binary($originalContent, $expectedMimeType, 'png');

        $thumbConfig = array(
            'size' => array(180, 180),
            'mode' => 'outbound',
        );

        $config = $this->getMockFilterConfiguration();
        $config
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('thumbnail')
            ->will($this->returnValue(array(
                'filters' => array(
                    'thumbnail' => $thumbConfig,
                ),
            )))
        ;

        $image = $this->getMockImage();
        $image
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue('aFilteredContent'))
        ;

        $imagine = $this->getMockImagine();
        $imagine
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue($image))
        ;

        $loader = $this->getMockLoader();
        $loader
            ->expects($this->once())
            ->method('load')
            ->with($this->identicalTo($image), $thumbConfig)
            ->will($this->returnArgument(0))
        ;

        $filterManager = new FilterManager($config, $imagine);
        $filterManager->addLoader('thumbnail', $loader);

        $filteredBinary = $filterManager->applyFilter($binary, 'thumbnail');

        $this->assertInstanceOf('Liip\ImagineBundle\Model\Binary', $filteredBinary);
        $this->assertEquals($expectedMimeType, $filteredBinary->getMimeType());
    }

    public function testAltersQualityOnApplyFilter()
    {
        $originalContent = 'aOriginalContent';
        $expectedQuality = 80;

        $binary = new Binary($originalContent, 'image/png', 'png');

        $thumbConfig = array(
            'size' => array(180, 180),
            'mode' => 'outbound',
        );

        $config = $this->getMockFilterConfiguration();
        $config
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('thumbnail')
            ->will($this->returnValue(array(
                'quality' => $expectedQuality,
                'filters' => array(
                    'thumbnail' => $thumbConfig,
                ),
            )))
        ;

        $image = $this->getMockImage();
        $image
            ->expects($this->once())
            ->method('get')
            ->with('png', array('quality' => $expectedQuality))
            ->will($this->returnValue('aFilteredContent'))
        ;

        $imagine = $this->getMockImagine();
        $imagine
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue($image))
        ;

        $loader = $this->getMockLoader();
        $loader
            ->expects($this->once())
            ->method('load')
            ->with($this->identicalTo($image), $thumbConfig)
            ->will($this->returnArgument(0))
        ;

        $filterManager = new FilterManager($config, $imagine);
        $filterManager->addLoader('thumbnail', $loader);

        $this->assertInstanceOf('Liip\ImagineBundle\Model\Binary', $filterManager->applyFilter($binary, 'thumbnail'));
    }

    public function testAlters100QualityIfNotSetOnApplyFilter()
    {
        $originalContent = 'aOriginalContent';
        $expectedQuality = 100;

        $binary = new Binary($originalContent, 'image/png', 'png');

        $thumbConfig = array(
            'size' => array(180, 180),
            'mode' => 'outbound',
        );

        $config = $this->getMockFilterConfiguration();
        $config
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('thumbnail')
            ->will($this->returnValue(array(
                'filters' => array(
                    'thumbnail' => $thumbConfig,
                ),
            )))
        ;

        $image = $this->getMockImage();
        $image
            ->expects($this->once())
            ->method('get')
            ->with('png', array('quality' => $expectedQuality))
            ->will($this->returnValue('aFilteredContent'))
        ;

        $imagine = $this->getMockImagine();
        $imagine
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue($image))
        ;

        $loader = $this->getMockLoader();
        $loader
            ->expects($this->once())
            ->method('load')
            ->with($this->identicalTo($image), $thumbConfig)
            ->will($this->returnArgument(0))
        ;

        $filterManager = new FilterManager($config, $imagine);
        $filterManager->addLoader('thumbnail', $loader);

        $this->assertInstanceOf('Liip\ImagineBundle\Model\Binary', $filterManager->applyFilter($binary, 'thumbnail'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoaderInterface
     */
    protected function getMockLoader()
    {
        return $this->getMock('Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface');
    }
}
