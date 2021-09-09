<?php
/*
 MIT License
 
 Copyright (c) 2021 Anweshan Roy Chowdhury
 
 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:
 
 The above copyright notice and this permission notice shall be included in all
 copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 SOFTWARE.
 
 */
namespace Anweshan\System;

/**
 * The interface ServerInterface, is implemented when writting a server class.
 *
 * @package Anweshan\System
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
interface ServerInterface {
    /**
     * Set manipulation api.
     * @param ApiInterface $api The manipulation api, an instanceof ApiInterface.
     */
    public function setApi($api);
    
    /**
     * Get manipulation api.
     * @return ApiInterface|null A property which is an instanceof SystemInterface.
     */
    public function getApi();
    
    /**
     * Set default manipulations.
     * @param string[] $defaults Default manipulations.
     */
    public function setDefaults(array $defaults = []);
    
    /**
     * Get default manipulations.
     * @return string[]|null Default manipulations.
     */
    public function getDefaults();
    
    /**
     * Set preset manipulations.
     * @param string[] $presets Preset manipulations.
     */
    public function setPresets(array $presets);
    
    /**
     * Get preset image manipulations.
     * @return string[]|null Preset image manipulations.
     */
    public function getPresets();
    
    /**
     * Get all manipulations params, including defaults and presets.
     * @param  string[] $params Manipulation parameters.
     * @return mixed All manipulation params.
     */
    public function getAllParams(array $params);
    
}