<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Itinerary</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #ff7f50, #ff6347);
            padding: 30px;
            color: white;
            position: relative;
        }
        
        .header h1 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 10px;
        }
        
        .header .subtitle {
            font-size: 2rem;
            font-weight: 300;
            opacity: 0.9;
        }
        
        .trip-details {
            padding: 30px;
            background: #fafafa;
            border-bottom: 3px solid #ff7f50;
        }
        
        .detail-row {
            width: 100%;
            margin-bottom: 15px;
            overflow: hidden;
        }
        
        .detail-item {
            width: 48%;
            float: left;
            margin-right: 4%;
        }
        
        .detail-item:last-child {
            margin-right: 0;
        }
        
        .detail-label {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: #333;
        }
        
        .detail-value {
            color: #666;
            font-size: 1rem;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        
        .itinerary {
            padding: 20px;
        }
        
        .day-block {
            width: 100%;
            margin-bottom: 30px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #eee;
        }
        
        .day-label {
            background: #ff7f50;
            color: white;
            width: 100px;
            height: 60px;
            float: left;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .day-content {
            margin-left: 100px;
            background: white;
            border-left: 1px solid #eee;
            min-height: 60px;
        }
        
        .day-header {
            background: #f8f8f8;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        
        .day-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
        }
        
        .day-theme {
            font-size: 1.1rem;
            color: #666;
            margin-top: 5px;
        }
        
        .activities {
            padding: 0;
        }
        
        .activity {
            width: 100%;
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            overflow: hidden;
        }
        
        .activity:hover {
            background-color: #f9f9f9;
        }
        
        .activity:last-child {
            border-bottom: none;
        }
        
        .time {
            width: 80px;
            float: left;
            font-weight: 600;
            color: #ff7f50;
            font-size: 0.95rem;
        }
        
        .activity-description {
            margin-left: 90px;
            color: #555;
            line-height: 1.4;
        }
        
        /* Clear floats */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .trip-details::after,
        .detail-row::after,
        .day-block::after,
        .activity::after {
            content: "";
            display: table;
            clear: both;
        }
            .container {
                margin: 10px;
                border-radius: 10px;
            }
            
            .header h1 {
                font-size: 2.5rem;
            }
            
            .header .subtitle {
                font-size: 1.5rem;
            }
            
            .detail-row {
                flex-direction: column;
                gap: 15px;
            }
            
            .day-block {
                flex-direction: column;
            }
            
            .day-label {
                writing-mode: horizontal-tb;
                text-orientation: mixed;
                padding: 15px;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Trip</h1>
            <div class="subtitle">Itinerary</div>
        </div>
        
        <div class="trip-details">
            <div class="detail-row">
                <div class="detail-item">
                    <div class="detail-label">Start Date</div>
                    <div class="detail-value">
                        @if(count($itineraries) > 0)
                            {{ \Carbon\Carbon::parse($locationOverview->start_date)->format('d/m/Y') }}
                        @else
                            --/--/----
                        @endif
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">End Date</div>
                    <div class="detail-value">
                        @if(count($itineraries) > 0)
                            {{ \Carbon\Carbon::parse($locationOverview->start_date)->addDays(count($itineraries) - 1)->format('d/m/Y') }}
                        @else
                            --/--/----
                        @endif
                    </div>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-item">
                    <div class="detail-label">Destination</div>
                    <div class="detail-value">{{ $tripDetails['location'] }}</div>
                </div>
            </div>
        </div>
        
        <div class="itinerary">

            @foreach ($itineraries as $itinerary)
                <div class="day-block">
                    <div class="day-label">DAY {{ $itinerary->day }}</div>
                    <div class="day-content">
                        <div class="day-header">
                            <div class="day-title">Time {{ \Carbon\Carbon::parse($locationOverview->start_date)->addDays($itinerary->day - 1)->format('D, d M') }}</div>
                            <div class="day-theme">Activity - Explore the City</div>
                        </div>
                        <div class="activities">
                            @foreach ($itinerary->activities as $activity)
                                <div class="activity">
                                    <div class="time">{{ $activity->best_time }}</div>
                                    <div class="activity-description">{{ $activity->description }} -- <strong>{{ $activity->name }}</strong></div>
                                </div>
                            @endforeach
                            <!-- <div class="activity">
                                <div class="time">11:00 AM</div>
                                <div class="activity-description">City sightseeing tour (visit landmarks, museums, etc.)</div>
                            </div>
                            <div class="activity">
                                <div class="time">3:00 PM</div>
                                <div class="activity-description">Explore a local market or shopping district</div>
                            </div>
                            <div class="activity">
                                <div class="time">8:00 PM</div>
                                <div class="activity-description">Evening stroll or visit to a nearby park</div>
                            </div> -->
                        </div>
                    </div>
                </div>
            @endforeach
            <!-- <div class="day-block">
                <div class="day-label">DAY 2</div>
                <div class="day-content">
                    <div class="day-header">
                        <div class="day-title">Time</div>
                        <div class="day-theme">Activity - Outdoor Adventure</div>
                    </div>
                    <div class="activities">
                        <div class="activity">
                            <div class="time">8:00 AM</div>
                            <div class="activity-description">Breakfast at the hotel or nearby café</div>
                        </div>
                        <div class="activity">
                            <div class="time">9:00 AM</div>
                            <div class="activity-description">Depart for a hiking trail or outdoor activity (e.g., kayaking, zip-lining)</div>
                        </div>
                        <div class="activity">
                            <div class="time">1:00 PM</div>
                            <div class="activity-description">Picnic lunch at a scenic spot</div>
                        </div>
                        <div class="activity">
                            <div class="time">6:00 PM</div>
                            <div class="activity-description">Return to the city</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="day-block">
                <div class="day-label">DAY 3</div>
                <div class="day-content">
                    <div class="day-header">
                        <div class="day-title">Time</div>
                        <div class="day-theme">Activity - Explore the City</div>
                    </div>
                    <div class="activities">
                        <div class="activity">
                            <div class="time">9:00 AM</div>
                            <div class="activity-description">Breakfast at a traditional breakfast spot</div>
                        </div>
                        <div class="activity">
                            <div class="time">10:00 AM</div>
                            <div class="activity-description">Visit historical sites or cultural attractions (museums, art galleries)</div>
                        </div>
                        <div class="activity">
                            <div class="time">1:00 PM</div>
                            <div class="activity-description">Lunch at a restaurant serving traditional dishes</div>
                        </div>
                        <div class="activity">
                            <div class="time">3:00 PM</div>
                            <div class="activity-description">Attend a cultural performance or workshop (music, dance, cooking class)</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="day-block">
                <div class="day-label">DAY 4</div>
                <div class="day-content">
                    <div class="day-header">
                        <div class="day-title">Time</div>
                        <div class="day-theme">Activity - Explore the City</div>
                    </div>
                    <div class="activities">
                        <div class="activity">
                            <div class="time">8:00 AM</div>
                            <div class="activity-description">Breakfast at the hotel or grab-and-go breakfast</div>
                        </div>
                        <div class="activity">
                            <div class="time">9:00 AM</div>
                            <div class="activity-description">Depart for a nearby town or attraction</div>
                        </div>
                        <div class="activity">
                            <div class="time">11:00 AM</div>
                            <div class="activity-description">Explore the town, visit landmarks, and local attractions</div>
                        </div>
                        <div class="activity">
                            <div class="time">3:00 PM</div>
                            <div class="activity-description">Continue exploring or engage in specific activities (beach, hiking, etc.)</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="day-block">
                <div class="day-label">DAY 5</div>
                <div class="day-content">
                    <div class="day-header">
                        <div class="day-title">Time</div>
                        <div class="day-theme">Activity - Explore the City</div>
                    </div>
                    <div class="activities">
                        <div class="activity">
                            <div class="time">9:00 AM</div>
                            <div class="activity-description">Leisurely breakfast at the hotel or favorite breakfast spot</div>
                        </div>
                        <div class="activity">
                            <div class="time">10:00 AM</div>
                            <div class="activity-description">Free time for last-minute shopping or relaxation</div>
                        </div>
                        <div class="activity">
                            <div class="time">3:00 PM</div>
                            <div class="activity-description">Final stroll around the city or visit to a nearby attraction</div>
                        </div>
                        <div class="activity">
                            <div class="time">8:00 PM</div>
                            <div class="activity-description">Departure</div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</body>
</html>