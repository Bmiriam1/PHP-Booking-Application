
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concert Booking System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Concert Booking System</h1>
        
        <div class="capacity-meter">
            <div class="meter-fill" id="capacityMeter"></div>
        </div>
        
        <div class="booking-form">
            <h2>Book a Ticket</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="ticketType">Ticket Type:</label>
                    <select name="ticketType" id="ticketType" required>
                        <option value="">-- Select Ticket Type --</option>
                        <option value="VVIP">VVIP (R3,000)</option>
                        <option value="VIP">VIP (R2,000)</option>
                        <option value="General">General (R500)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select name="gender" id="gender" required>
                        <option value="">-- Select Gender --</option>
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="age">Age:</label>
                    <input type="number" name="age" id="age" min="16" max="35" required>
                </div>
                
                <button type="submit" name="bookTicket">Book Ticket</button>
            </form>
        </div>
        
        <?php
        // PHP Code Start
        class ConcertBookingSystem {
            private $ticketsSold = 0;
            private $maxCapacity = 60000;
            private $ticketPrices = [
                'VVIP' => 3000,
                'VIP' => 2000,
                'General' => 500
            ];
            private $salesData = [
                'Female' => ['16-21' => 0, '22-35' => 0],
                'Male' => ['16-21' => 0, '22-35' => 0]
            ];
            private $seatsAssigned = [];
            private $totalRevenue = 0;
            private $bookingHistory = [];

            public function bookTicket($ticketType, $gender, $age) {
                if ($age < 16) return ['status' => 'error', 'message' => "Minimum age is 16"];
                if ($this->ticketsSold >= $this->maxCapacity) return ['status' => 'error', 'message' => "Sold out"];
                if (!array_key_exists($ticketType, $this->ticketPrices)) return ['status' => 'error', 'message' => "Invalid ticket"];
                if ($gender !== 'Female' && $gender !== 'Male') return ['status' => 'error', 'message' => "Invalid gender"];

                $ageGroup = ($age >= 16 && $age <= 21) ? '16-21' : '22-35';
                $seatNumber = $this->generateSeatNumber($ticketType);
                $price = $this->ticketPrices[$ticketType];

                $this->totalRevenue += $price;
                $this->salesData[$gender][$ageGroup]++;
                $this->ticketsSold++;
                $this->seatsAssigned[] = $seatNumber;

                $booking = [
                    'ticket_type' => $ticketType,
                    'price' => $price,
                    'seat_number' => $seatNumber,
                    'gender' => $gender,
                    'age_group' => $ageGroup,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
                array_unshift($this->bookingHistory, $booking);

                return ['status' => 'success', 'booking' => $booking];
            }

            private function generateSeatNumber($ticketType) {
                $prefix = [
                    'VVIP' => 'VV',
                    'VIP' => 'VI',
                    'General' => 'GN'
                ][$ticketType];
                return $prefix . str_pad($this->ticketsSold + 1, 5, '0', STR_PAD_LEFT);
            }

            public function getSalesReport() {
                return [
                    'sales_data' => $this->salesData,
                    'total_tickets_sold' => $this->ticketsSold,
                    'remaining_capacity' => $this->maxCapacity - $this->ticketsSold,
                    'total_revenue' => $this->totalRevenue,
                    'capacity_percentage' => ($this->ticketsSold / $this->maxCapacity) * 100
                ];
            }

            public function getBookingHistory($limit = 5) {
                return array_slice($this->bookingHistory, 0, $limit);
            }
        }

        session_start();
        if (!isset($_SESSION['bookingSystem'])) {
            $_SESSION['bookingSystem'] = new ConcertBookingSystem();
            
            // Sample data
            $sampleBookings = [
                ['VVIP', 'Female', 18], ['VIP', 'Female', 20], ['General', 'Female', 19],
                ['General', 'Female', 17], ['VIP', 'Female', 21], ['VVIP', 'Female', 25],
                ['VIP', 'Female', 30], ['General', 'Female', 28], ['General', 'Female', 22],
                ['VIP', 'Female', 35], ['VVIP', 'Male', 18], ['VIP', 'Male', 20],
                ['General', 'Male', 19], ['General', 'Male', 17], ['VIP', 'Male', 21],
                ['VVIP', 'Male', 16], ['VVIP', 'Male', 25], ['VIP', 'Male', 30],
                ['General', 'Male', 28], ['VVIP', 'Male', 22]
            ];
            
            foreach ($sampleBookings as $booking) {
                $_SESSION['bookingSystem']->bookTicket($booking[0], $booking[1], $booking[2]);
            }
        }

        $system = $_SESSION['bookingSystem'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bookTicket'])) {
            $result = $system->bookTicket(
                $_POST['ticketType'] ?? '',
                $_POST['gender'] ?? '',
                $_POST['age'] ?? 0
            );
            
            if ($result['status'] === 'success') {
                echo '<div class="success">Ticket booked! Seat: ' . $result['booking']['seat_number'] . '</div>';
            } else {
                echo '<div class="error">' . $result['message'] . '</div>';
            }
        }

        $salesReport = $system->getSalesReport();
        $recentBookings = $system->getBookingHistory(5);
        ?>
        
        <div class="recent-bookings">
            <h2>Recent Bookings</h2>
            <?php foreach ($recentBookings as $booking): ?>
                <div class="ticket <?php echo strtolower($booking['ticket_type']); ?>">
                    <h3><?php echo $booking['ticket_type']; ?> Ticket</h3>
                    <p><strong>Seat Number:</strong> <?php echo $booking['seat_number']; ?></p>
                    <p><strong>Price:</strong> R<?php echo number_format($booking['price'], 2); ?></p>
                    <p><strong>Gender:</strong> <?php echo $booking['gender']; ?></p>
                    <p><strong>Age Group:</strong> <?php echo $booking['age_group']; ?></p>
                    <p><strong>Booked at:</strong> <?php echo $booking['timestamp']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="sales-report">
            <h2>Sales Report</h2>
            <p><strong>Total Tickets Sold:</strong> <?php echo number_format($salesReport['total_tickets_sold']); ?></p>
            <p><strong>Remaining Capacity:</strong> <?php echo number_format($salesReport['remaining_capacity']); ?></p>
            <p><strong>Total Revenue:</strong> R<?php echo number_format($salesReport['total_revenue'], 2); ?></p>
            
            <h3>Sales by Demographic</h3>
            <table>
                <tr>
                    <th>Gender</th>
                    <th>Age 16-21</th>
                    <th>Age 22-35</th>
                    <th>Total</th>
                </tr>
                <?php foreach ($salesReport['sales_data'] as $gender => $ageGroups): ?>
                    <tr>
                        <td><?php echo $gender; ?></td>
                        <td><?php echo $ageGroups['16-21']; ?></td>
                        <td><?php echo $ageGroups['22-35']; ?></td>
                        <td><?php echo $ageGroups['16-21'] + $ageGroups['22-35']; ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td><strong>Total</strong></td>
                    <td><?php echo $salesReport['sales_data']['Female']['16-21'] + $salesReport['sales_data']['Male']['16-21']; ?></td>
                    <td><?php echo $salesReport['sales_data']['Female']['22-35'] + $salesReport['sales_data']['Male']['22-35']; ?></td>
                    <td><?php echo $salesReport['total_tickets_sold']; ?></td>
                </tr>
            </table>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script>
        updateCapacityMeter(<?php echo $salesReport['capacity_percentage']; ?>);
    </script>
</body>
</html>