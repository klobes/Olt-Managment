# 🗺️ Network Topology & Fiber Management Guide

## Overview

Ky dokument përshkruan sistemin e menaxhimit të topologjisë së rrjetit dhe fibrave optike.

---

## Konceptet Bazë

### 1. Struktura Hierarkike

```
OLT (Optical Line Terminal)
  ↓
Fiber Cable (Kabllo Optike)
  ↓
Junction Box (Xhundo)
  ↓
Splice Cassette (Kasetë Bashkimi)
  ↓
Splitter (Ndarës 1:N)
  ↓
Fiber Cable (Kabllo Optike)
  ↓
ONU (Optical Network Unit)
```

### 2. Komponentët Kryesorë

#### A. Fiber Cable (Kabllo Optike)
- Përmban shumë fibra (2, 4, 8, 12, 24, 48, 96, 144)
- Llojet: Single-mode, Multi-mode, Armored, Aerial, Underground
- Gjatësia: Në metra
- Status: Active, Inactive, Damaged, Maintenance

#### B. Junction Box (Xhundo)
- Vendndodhja: Street, Building, Pole, Underground, Wall-mount
- Kapaciteti: Numri i fibrave që mund të menaxhojë
- GPS Coordinates: Latitude, Longitude
- Photos: Dokumentim fotografik

#### C. Splice Cassette (Kasetë Bashkimi)
- Përmban bashkime të fibrave (fusion ose mechanical)
- Kapaciteti: 12, 24, 48 bashkime
- Vendndodhja: Brenda junction box
- Test Results: OTDR measurements

#### D. Splitter (Ndarës)
- Llojet: 1:2, 1:4, 1:8, 1:16, 1:32, 1:64
- Insertion Loss: Humbja e sinjalit në dB
- Input/Output Ports: Portat e hyrjes dhe daljes
- Cascade Support: Mundësi për splitter në cascade

---

## Database Schema

### Relacionet

```
fiber_cables
  ↓ (1:N)
cable_segments
  ↓ (N:1)
junction_boxes
  ↓ (1:N)
splice_cassettes
  ↓ (1:N)
fiber_splices
  ↓ (N:1)
splitters
  ↓ (1:N)
splitter_connections
```

---

## Shembuj Praktikë

### Skenar 1: Instalimi i një Klienti të Ri

#### Hapi 1: Krijoni Fiber Cable nga OLT në Junction Box

```php
$cable = FiberCable::create([
    'cable_code' => 'FO-001',
    'cable_name' => 'OLT-TIRANE-01 to JB-001',
    'cable_type' => 'underground',
    'fiber_count' => 48,
    'length' => 500.00, // meters
    'manufacturer' => 'Corning',
    'installation_date' => '2024-01-01',
    'status' => 'active',
]);
```

#### Hapi 2: Krijoni Junction Box

```php
$junctionBox = JunctionBox::create([
    'box_code' => 'JB-001',
    'box_name' => 'Junction Box - Rruga e Kavajes',
    'box_type' => 'street',
    'capacity' => 96,
    'latitude' => 41.3275,
    'longitude' => 19.8187,
    'address' => 'Rruga e Kavajes, Tirane',
    'installation_date' => '2024-01-01',
    'status' => 'active',
]);
```

#### Hapi 3: Krijoni Cable Segment (OLT → Junction Box)

```php
$segment1 = CableSegment::create([
    'fiber_cable_id' => $cable->id,
    'fiber_number' => 1, // Fibra #1 në kabllo
    'source_type' => 'OltDevice',
    'source_id' => $olt->id,
    'source_port' => 'PON 1/1/1',
    'destination_type' => 'JunctionBox',
    'destination_id' => $junctionBox->id,
    'destination_port' => 'IN-1',
    'segment_length' => 500.00,
    'attenuation' => 0.5, // dB
    'status' => 'active',
]);
```

#### Hapi 4: Krijoni Splitter në Junction Box

```php
$splitter = Splitter::create([
    'splitter_code' => 'SPL-001',
    'splitter_name' => 'Splitter 1:16 - JB-001',
    'splitter_type' => '1:16',
    'input_ports' => 1,
    'output_ports' => 16,
    'insertion_loss' => 13.5, // dB for 1:16
    'junction_box_id' => $junctionBox->id,
    'installation_date' => '2024-01-01',
    'status' => 'active',
]);
```

#### Hapi 5: Krijoni Fiber Cable nga Junction Box në ONU

```php
$cable2 = FiberCable::create([
    'cable_code' => 'FO-002',
    'cable_name' => 'JB-001 to Client Area',
    'cable_type' => 'aerial',
    'fiber_count' => 24,
    'length' => 200.00,
    'installation_date' => '2024-01-01',
    'status' => 'active',
]);
```

#### Hapi 6: Krijoni Cable Segment (Splitter → ONU)

```php
$segment2 = CableSegment::create([
    'fiber_cable_id' => $cable2->id,
    'fiber_number' => 1,
    'source_type' => 'Splitter',
    'source_id' => $splitter->id,
    'source_port' => 'OUT-1',
    'destination_type' => 'Onu',
    'destination_id' => $onu->id,
    'destination_port' => 'PON',
    'segment_length' => 200.00,
    'attenuation' => 0.2,
    'status' => 'active',
]);
```

#### Hapi 7: Krijoni Splitter Connection

```php
$connection = SplitterConnection::create([
    'splitter_id' => $splitter->id,
    'input_cable_segment_id' => $segment1->id,
    'input_port' => 'IN',
    'output_cable_segment_id' => $segment2->id,
    'output_port_number' => 1,
    'port_loss' => 13.5,
    'status' => 'active',
]);
```

---

## Fiber Path Tracing

### Gjurmimi i Rrugës së Plotë

```php
class FiberPathTracer
{
    public function tracePath(Onu $onu): array
    {
        $path = [];
        $currentSegment = $this->findSegmentToOnu($onu);
        
        while ($currentSegment) {
            $path[] = [
                'type' => 'cable_segment',
                'cable' => $currentSegment->fiberCable,
                'fiber_number' => $currentSegment->fiber_number,
                'length' => $currentSegment->segment_length,
                'attenuation' => $currentSegment->attenuation,
            ];
            
            // Gjej source equipment
            $source = $this->getEquipment(
                $currentSegment->source_type,
                $currentSegment->source_id
            );
            
            $path[] = [
                'type' => strtolower($currentSegment->source_type),
                'equipment' => $source,
            ];
            
            // Nëse source është OLT, ndalo
            if ($currentSegment->source_type === 'OltDevice') {
                break;
            }
            
            // Vazhdo me segment-in tjetër
            $currentSegment = $this->findPreviousSegment($source);
        }
        
        return array_reverse($path);
    }
    
    public function calculateTotalLoss(array $path): float
    {
        $totalLoss = 0;
        
        foreach ($path as $item) {
            if ($item['type'] === 'cable_segment') {
                $totalLoss += $item['attenuation'];
            } elseif ($item['type'] === 'splitter') {
                $totalLoss += $item['equipment']->insertion_loss;
            } elseif ($item['type'] === 'splice') {
                $totalLoss += $item['equipment']->splice_loss ?? 0.1;
            }
        }
        
        return $totalLoss;
    }
}
```

### Përdorimi

```php
$tracer = new FiberPathTracer();
$path = $tracer->tracePath($onu);
$totalLoss = $tracer->calculateTotalLoss($path);

// Shfaq rrugën
foreach ($path as $item) {
    echo "{$item['type']}: {$item['equipment']->name}\n";
    if ($item['type'] === 'cable_segment') {
        echo "  Length: {$item['length']}m, Loss: {$item['attenuation']}dB\n";
    }
}

echo "Total Loss: {$totalLoss}dB\n";
```

---

## Topology Visualization

### Frontend Implementation (JavaScript)

```javascript
// resources/js/topology-viewer.js

class TopologyViewer {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.svg = d3.select(this.container).append('svg');
        this.width = this.container.clientWidth;
        this.height = this.container.clientHeight;
    }
    
    loadTopology(onuId) {
        fetch(`/api/topology/trace/${onuId}`)
            .then(response => response.json())
            .then(data => this.renderTopology(data));
    }
    
    renderTopology(path) {
        // Clear previous
        this.svg.selectAll('*').remove();
        
        const nodes = [];
        const links = [];
        
        // Build nodes and links from path
        path.forEach((item, index) => {
            if (item.type !== 'cable_segment') {
                nodes.push({
                    id: `${item.type}-${item.equipment.id}`,
                    type: item.type,
                    name: item.equipment.name,
                    x: (index / path.length) * this.width,
                    y: this.height / 2,
                });
            }
            
            if (index > 0 && item.type === 'cable_segment') {
                links.push({
                    source: nodes[nodes.length - 1].id,
                    target: nodes[nodes.length].id,
                    length: item.length,
                    loss: item.attenuation,
                });
            }
        });
        
        // Render links
        this.svg.selectAll('line')
            .data(links)
            .enter()
            .append('line')
            .attr('x1', d => this.getNodeX(d.source))
            .attr('y1', d => this.getNodeY(d.source))
            .attr('x2', d => this.getNodeX(d.target))
            .attr('y2', d => this.getNodeY(d.target))
            .attr('stroke', '#999')
            .attr('stroke-width', 2);
        
        // Render nodes
        this.svg.selectAll('circle')
            .data(nodes)
            .enter()
            .append('circle')
            .attr('cx', d => d.x)
            .attr('cy', d => d.y)
            .attr('r', 20)
            .attr('fill', d => this.getNodeColor(d.type));
        
        // Render labels
        this.svg.selectAll('text')
            .data(nodes)
            .enter()
            .append('text')
            .attr('x', d => d.x)
            .attr('y', d => d.y + 40)
            .attr('text-anchor', 'middle')
            .text(d => d.name);
    }
    
    getNodeColor(type) {
        const colors = {
            'oltdevice': '#4CAF50',
            'junctionbox': '#2196F3',
            'splitter': '#FF9800',
            'onu': '#F44336',
        };
        return colors[type] || '#999';
    }
}

// Initialize
const viewer = new TopologyViewer('topology-container');
viewer.loadTopology(onuId);
```

---

## API Endpoints për Topology

```php
// routes/api.php

Route::group(['prefix' => 'topology'], function () {
    // Get fiber path for ONU
    Route::get('trace/{onu}', [TopologyController::class, 'tracePath']);
    
    // Get all equipment in junction box
    Route::get('junction-box/{id}', [TopologyController::class, 'getJunctionBoxDetails']);
    
    // Get available splitter ports
    Route::get('splitter/{id}/available-ports', [TopologyController::class, 'getAvailablePorts']);
    
    // Calculate optical budget
    Route::post('calculate-budget', [TopologyController::class, 'calculateOpticalBudget']);
    
    // Find optimal path
    Route::post('find-path', [TopologyController::class, 'findOptimalPath']);
});
```

---

## Optical Budget Calculator

```php
class OpticalBudgetCalculator
{
    // Typical values
    const FIBER_LOSS_PER_KM = 0.35; // dB/km for 1310nm
    const SPLICE_LOSS = 0.1; // dB per splice
    const CONNECTOR_LOSS = 0.5; // dB per connector
    
    // Splitter losses
    const SPLITTER_LOSS = [
        '1:2' => 3.5,
        '1:4' => 7.0,
        '1:8' => 10.5,
        '1:16' => 13.5,
        '1:32' => 16.5,
        '1:64' => 19.5,
    ];
    
    public function calculate(array $path): array
    {
        $totalLoss = 0;
        $breakdown = [];
        
        foreach ($path as $item) {
            $loss = 0;
            
            switch ($item['type']) {
                case 'cable_segment':
                    $lengthKm = $item['length'] / 1000;
                    $loss = $lengthKm * self::FIBER_LOSS_PER_KM;
                    $breakdown[] = [
                        'component' => "Cable ({$item['length']}m)",
                        'loss' => $loss,
                    ];
                    break;
                    
                case 'splitter':
                    $loss = self::SPLITTER_LOSS[$item['equipment']->splitter_type];
                    $breakdown[] = [
                        'component' => "Splitter {$item['equipment']->splitter_type}",
                        'loss' => $loss,
                    ];
                    break;
                    
                case 'splice':
                    $loss = self::SPLICE_LOSS;
                    $breakdown[] = [
                        'component' => 'Splice',
                        'loss' => $loss,
                    ];
                    break;
                    
                case 'connector':
                    $loss = self::CONNECTOR_LOSS;
                    $breakdown[] = [
                        'component' => 'Connector',
                        'loss' => $loss,
                    ];
                    break;
            }
            
            $totalLoss += $loss;
        }
        
        return [
            'total_loss' => round($totalLoss, 2),
            'breakdown' => $breakdown,
            'olt_tx_power' => 3.0, // dBm (typical)
            'expected_rx_power' => round(3.0 - $totalLoss, 2),
            'status' => $this->evaluateStatus(3.0 - $totalLoss),
        ];
    }
    
    protected function evaluateStatus(float $rxPower): string
    {
        if ($rxPower >= -8 && $rxPower <= -3) {
            return 'excellent';
        } elseif ($rxPower >= -15 && $rxPower < -8) {
            return 'good';
        } elseif ($rxPower >= -25 && $rxPower < -15) {
            return 'acceptable';
        } else {
            return 'poor';
        }
    }
}
```

---

## Maintenance Tracking

### Regjistrimi i Maintenance

```php
$maintenance = EquipmentMaintenance::create([
    'equipment_type' => 'Splitter',
    'equipment_id' => $splitter->id,
    'maintenance_type' => 'inspection',
    'maintenance_date' => now(),
    'technician' => 'John Doe',
    'description' => 'Routine inspection and cleaning',
    'cost' => 50.00,
    'photos' => [
        'before.jpg',
        'after.jpg',
    ],
    'notes' => 'All ports functioning correctly',
]);
```

---

## Best Practices

### 1. Naming Convention

```
Fiber Cables: FO-[LOCATION]-[NUMBER]
  Example: FO-TIRANE-001

Junction Boxes: JB-[LOCATION]-[NUMBER]
  Example: JB-KAVAJE-001

Splitters: SPL-[LOCATION]-[TYPE]-[NUMBER]
  Example: SPL-KAVAJE-1:16-001

Cassettes: CAS-[LOCATION]-[NUMBER]
  Example: CAS-KAVAJE-001
```

### 2. Documentation

- Fotografoni çdo junction box
- Dokumentoni çdo bashkim
- Mbani një diagram të topology-së
- Etiketoni të gjitha kabllot
- Përdorni QR codes për equipment

### 3. Testing

- Testoni çdo bashkim me OTDR
- Matni optical power në çdo pikë
- Verifikoni total loss
- Dokumentoni test results

---

## Mobile App Features (Future)

### Field Technician Tools

1. **GPS Navigation** - Navigim në junction boxes
2. **QR Code Scanning** - Identifikim i shpejtë i equipment
3. **Photo Documentation** - Foto të punës
4. **Work Order Management** - Menaxhim i task-eve
5. **Offline Mode** - Punë pa internet
6. **Signature Capture** - Nënshkrime dixhitale

---

**Last Updated**: 2024-01-01  
**Version**: 2.0.0 (Planned)