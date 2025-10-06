<?php

namespace Botble\FiberhomeOltManager\Services\Vendors;

use Botble\FiberhomeOltManager\Models\OLT;
use Botble\FiberhomeOltManager\Models\Onu;

/**
 * Interface VendorDriverInterface
 * 
 * Defines the contract for vendor-specific OLT drivers
 * Each vendor (Fiberhome, Huawei, ZTE) must implement this interface
 */
interface VendorDriverInterface
{
    /**
     * Get system information from OLT
     *
     * @param OLT $olt
     * @return array
     */
    public function getSystemInfo(OLT $olt): array;

    /**
     * Get all cards/slots information
     *
     * @param OLT $olt
     * @return array
     */
    public function getCards(OLT $olt): array;

    /**
     * Get all PON ports information
     *
     * @param OLT $olt
     * @return array
     */
    public function getPonPorts(OLT $olt): array;

    /**
     * Get all ONUs from OLT
     *
     * @param OLT $olt
     * @return array
     */
    public function getOnus(OLT $olt): array;

    /**
     * Get specific ONU information
     *
     * @param OLT $olt
     * @param string $onuId
     * @return array
     */
    public function getOnuInfo(OLT $olt, string $onuId): array;

    /**
     * Get ONU optical power (RX/TX)
     *
     * @param OLT $olt
     * @param string $onuId
     * @return array
     */
    public function getOnuOpticalPower(OLT $olt, string $onuId): array;

    /**
     * Get ONU distance
     *
     * @param OLT $olt
     * @param string $onuId
     * @return float
     */
    public function getOnuDistance(OLT $olt, string $onuId): float;

    /**
     * Enable ONU
     *
     * @param OLT $olt
     * @param Onu $onu
     * @return bool
     */
    public function enableOnu(OLT $olt, Onu $onu): bool;

    /**
     * Disable ONU
     *
     * @param OLT $olt
     * @param Onu $onu
     * @return bool
     */
    public function disableOnu(OLT $olt, Onu $onu): bool;

    /**
     * Reboot ONU
     *
     * @param OLT $olt
     * @param Onu $onu
     * @return bool
     */
    public function rebootOnu(OLT $olt, Onu $onu): bool;

    /**
     * Configure ONU bandwidth profile
     *
     * @param OLT $olt
     * @param Onu $onu
     * @param array $profile
     * @return bool
     */
    public function configureBandwidth(OLT $olt, Onu $onu, array $profile): bool;

    /**
     * Configure ONU VLAN
     *
     * @param OLT $olt
     * @param Onu $onu
     * @param array $vlanConfig
     * @return bool
     */
    public function configureVlan(OLT $olt, Onu $onu, array $vlanConfig): bool;

    /**
     * Get performance metrics (CPU, Memory, Temperature)
     *
     * @param OLT $olt
     * @return array
     */
    public function getPerformanceMetrics(OLT $olt): array;

    /**
     * Discover new ONUs
     *
     * @param OLT $olt
     * @return array
     */
    public function discoverOnus(OLT $olt): array;

    /**
     * Add ONU to whitelist
     *
     * @param OLT $olt
     * @param array $onuData
     * @return bool
     */
    public function addOnuToWhitelist(OLT $olt, array $onuData): bool;

    /**
     * Remove ONU from whitelist
     *
     * @param OLT $olt
     * @param Onu $onu
     * @return bool
     */
    public function removeOnuFromWhitelist(OLT $olt, Onu $onu): bool;

    /**
     * Get vendor-specific OID mappings
     *
     * @return array
     */
    public function getOidMappings(): array;

    /**
     * Validate OLT connection
     *
     * @param OLT $olt
     * @return bool
     */
    public function validateConnection(OLT $olt): bool;

    /**
     * Get vendor name
     *
     * @return string
     */
    public function getVendorName(): string;

    /**
     * Get supported models
     *
     * @return array
     */
    public function getSupportedModels(): array;
}