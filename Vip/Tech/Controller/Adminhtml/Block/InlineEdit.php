<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vip\Tech\Controller\Adminhtml\Block;

use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepository;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\Data\BlockInterface;



 

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    

    /** @var OrderRepository  */
    protected $OrderRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param OrderRepository $OrderRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        OrderRepository $OrderRepository,		
		\Magento\Sales\Model\OrderFactory $OrderFactory,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->OrderRepository = $OrderRepository;
		$this->_OrderFactory = $OrderFactory;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $blockId) {
					
					$post = $this->_OrderFactory->create()->load($blockId);
					
					
					try {
                $postData = $postItems[$blockId];//todo: handle dates
				
				//$postData['customer_firstname'] ='kalala';
				$postData['grand_total'] =18.24;
				$postData['shipping_name'] ='plplp';
				//echo"<pre>"; print_r($postData); die('kjsdkdkj');
				
				 // echo"<pre>"; print_r($post->getData()); exit;
				//  $post->setData(array_merge($post->getData(), $postItems[$blockId]));
                $post->addData($postData);
                $post->save();
            } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithBlockId(
                            $block,
                            __($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add block title to error message
     *
     * @param BlockInterface $block
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithBlockId(BlockInterface $block, $errorText)
    {
        return '[Block ID: ' . $block->getId() . '] ' . $errorText;
    }
}
