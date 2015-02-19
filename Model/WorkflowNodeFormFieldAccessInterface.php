<?php

namespace JbNahan\Bundle\WorkflowManagerBundle\Model;

interface WorkflowNodeFormFieldAccessInterface
{
    /**
     * @param string $name
     * @return object
     */
    public function setFieldInternalName($name);
    
    /**
     * @return string
     */
    public function getFieldInternalName();

    /**
     * @param string $name
     * @return object
     */
    public function setFormInternalName($name);
    
    /**
     * @return string
     */
    public function getFormInternalName();

    
}
