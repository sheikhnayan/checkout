<?php

namespace App\Helpers;

class UsLocations
{
    /**
     * Get all 50 US States and their major cities.
     *
     * @return array<string, array<int, string>>
     */
    public static function getStatesAndCities(): array
    {
        return [
            'Alabama' => ['Birmingham', 'Huntsville', 'Mobile', 'Montgomery', 'Tuscaloosa', 'Auburn', 'Decatur', 'Dothan', 'Hoover', 'Florence'],
            'Alaska' => ['Anchorage', 'Fairbanks', 'Juneau', 'Sitka', 'Ketchikan', 'Wasilla', 'Kenai', 'Kodiak', 'Bethel', 'Palmer'],
            'Arizona' => ['Phoenix', 'Tucson', 'Mesa', 'Chandler', 'Scottsdale', 'Glendale', 'Gilbert', 'Tempe', 'Peoria', 'Surprise', 'Flagstaff', 'Yuma'],
            'Arkansas' => ['Little Rock', 'Fort Smith', 'Fayetteville', 'Springdale', 'Jonesboro', 'Rogers', 'Conway', 'Bentonville', 'Pine Bluff'],
            'California' => ['Los Angeles', 'San Francisco', 'San Diego', 'San Jose', 'Sacramento', 'Fresno', 'Long Beach', 'Oakland', 'Bakersfield', 'Anaheim', 'Santa Ana', 'Riverside', 'Stockton', 'Irvine', 'Chula Vista', 'Fremont', 'San Bernardino', 'Modesto', 'Fontana', 'Santa Clarita', 'Glendale', 'Pasadena', 'Santa Barbara', 'Beverly Hills', 'Palm Springs', 'West Hollywood', 'San Mateo', 'Santa Monica'],
            'Colorado' => ['Denver', 'Colorado Springs', 'Aurora', 'Fort Collins', 'Lakewood', 'Thornton', 'Arvada', 'Westminster', 'Pueblo', 'Greeley', 'Boulder', 'Aspen', 'Vail'],
            'Connecticut' => ['Bridgeport', 'Stamford', 'New Haven', 'Hartford', 'Waterbury', 'Norwalk', 'Danbury', 'New Britain', 'Greenwich'],
            'Delaware' => ['Wilmington', 'Dover', 'Newark', 'Middletown', 'Smyrna', 'Milford', 'Seaford', 'Georgetown'],
            'Florida' => ['Miami', 'Orlando', 'Tampa', 'Jacksonville', 'Fort Lauderdale', 'St. Petersburg', 'Hialeah', 'Tallahassee', 'Port St. Lucie', 'Cape Coral', 'Pembroke Pines', 'Hollywood', 'Gainesville', 'Miramar', 'Coral Springs', 'Clearwater', 'Palm Bay', 'Pompano Beach', 'West Palm Beach', 'Boca Raton', 'Key West', 'Miami Beach', 'Naples', 'Sarasota', 'Pensacola', 'Daytona Beach'],
            'Georgia' => ['Atlanta', 'Augusta', 'Columbus', 'Macon', 'Savannah', 'Athens', 'Sandy Springs', 'Roswell', 'Johns Creek', 'Warner Robins', 'Alpharetta'],
            'Hawaii' => ['Honolulu', 'Hilo', 'Kailua', 'Kapolei', 'Kaneohe', 'Lahaina', 'Kahului', 'Lihue'],
            'Idaho' => ['Boise', 'Meridian', 'Nampa', 'Idaho Falls', 'Caldwell', 'Pocatello', 'Coeur d\'Alene', 'Twin Falls'],
            'Illinois' => ['Chicago', 'Aurora', 'Joliet', 'Naperville', 'Rockford', 'Elgin', 'Springfield', 'Peoria', 'Champaign', 'Waukegan', 'Evanston'],
            'Indiana' => ['Indianapolis', 'Fort Wayne', 'Evansville', 'South Bend', 'Carmel', 'Fishers', 'Bloomington', 'Hammond', 'Gary'],
            'Iowa' => ['Des Moines', 'Cedar Rapids', 'Davenport', 'Sioux City', 'Iowa City', 'Waterloo', 'Ames', 'Council Bluffs'],
            'Kansas' => ['Wichita', 'Overland Park', 'Kansas City', 'Olathe', 'Topeka', 'Lawrence', 'Shawnee', 'Manhattan'],
            'Kentucky' => ['Louisville', 'Lexington', 'Bowling Green', 'Owensboro', 'Covington', 'Richmond', 'Georgetown', 'Florence'],
            'Louisiana' => ['New Orleans', 'Baton Rouge', 'Shreveport', 'Lafayette', 'Lake Charles', 'Kenner', 'Bossier City', 'Monroe'],
            'Maine' => ['Portland', 'Lewiston', 'Bangor', 'South Portland', 'Auburn', 'Biddeford', 'Sanford', 'Augusta'],
            'Maryland' => ['Baltimore', 'Columbia', 'Germantown', 'Silver Spring', 'Waldorf', 'Frederick', 'Ellicott City', 'Glen Burnie', 'Gaithersburg', 'Rockville', 'Annapolis'],
            'Massachusetts' => ['Boston', 'Worcester', 'Springfield', 'Cambridge', 'Lowell', 'Brockton', 'Quincy', 'Lynn', 'New Bedford', 'Fall River', 'Newton', 'Somerville', 'Salem'],
            'Michigan' => ['Detroit', 'Grand Rapids', 'Warren', 'Sterling Heights', 'Ann Arbor', 'Lansing', 'Dearborn', 'Livonia', 'Troy', 'Westland', 'Flint', 'Kalamazoo'],
            'Minnesota' => ['Minneapolis', 'St. Paul', 'Rochester', 'Duluth', 'Bloomington', 'Brooklyn Park', 'Plymouth', 'Woodbury', 'St. Cloud'],
            'Mississippi' => ['Jackson', 'Gulfport', 'Southaven', 'Biloxi', 'Hattiesburg', 'Olive Branch', 'Tupelo', 'Meridian'],
            'Missouri' => ['Kansas City', 'St. Louis', 'Springfield', 'Columbia', 'Independence', 'Lee\'s Summit', 'O\'Fallon', 'St. Joseph', 'St. Charles'],
            'Montana' => ['Billings', 'Missoula', 'Great Falls', 'Bozeman', 'Butte', 'Helena', 'Kalispell'],
            'Nebraska' => ['Omaha', 'Lincoln', 'Bellevue', 'Grand Island', 'Kearney', 'Fremont', 'Hastings'],
            'Nevada' => ['Las Vegas', 'Henderson', 'Reno', 'North Las Vegas', 'Sparks', 'Carson City', 'Elko'],
            'New Hampshire' => ['Manchester', 'Nashua', 'Concord', 'Derry', 'Dover', 'Rochester', 'Salem', 'Portsmouth'],
            'New Jersey' => ['Newark', 'Jersey City', 'Paterson', 'Elizabeth', 'Lakewood', 'Edison', 'Woodbridge', 'Toms River', 'Hamilton', 'Trenton', 'Clifton', 'Camden', 'Atlantic City', 'Hoboken'],
            'New Mexico' => ['Albuquerque', 'Las Cruces', 'Rio Rancho', 'Santa Fe', 'Roswell', 'Farmington', 'Clovis'],
            'New York' => ['New York City', 'Buffalo', 'Rochester', 'Yonkers', 'Syracuse', 'Albany', 'New Rochelle', 'Mount Vernon', 'Schenectady', 'Utica', 'White Plains', 'Hempstead', 'Troy', 'Binghamton', 'Saratoga Springs', 'Hampton Bays', 'Montauk'],
            'North Carolina' => ['Charlotte', 'Raleigh', 'Greensboro', 'Durham', 'Winston-Salem', 'Fayetteville', 'Cary', 'Wilmington', 'High Point', 'Asheville', 'Concord'],
            'North Dakota' => ['Fargo', 'Bismarck', 'Grand Forks', 'Minot', 'West Fargo', 'Williston', 'Dickinson'],
            'Ohio' => ['Columbus', 'Cleveland', 'Cincinnati', 'Toledo', 'Akron', 'Dayton', 'Parma', 'Canton', 'Youngstown', 'Lorain', 'Hamilton'],
            'Oklahoma' => ['Oklahoma City', 'Tulsa', 'Norman', 'Broken Arrow', 'Lawton', 'Edmond', 'Moore', 'Midwest City', 'Enid'],
            'Oregon' => ['Portland', 'Salem', 'Eugene', 'Gresham', 'Hillsboro', 'Beaverton', 'Bend', 'Medford', 'Springfield', 'Corvallis'],
            'Pennsylvania' => ['Philadelphia', 'Pittsburgh', 'Allentown', 'Reading', 'Erie', 'Upper Darby', 'Scranton', 'Bethlehem', 'Lancaster', 'Harrisburg', 'York'],
            'Rhode Island' => ['Providence', 'Cranston', 'Warwick', 'Pawtucket', 'East Providence', 'Woonsocket', 'Newport'],
            'South Carolina' => ['Charleston', 'Columbia', 'North Charleston', 'Mount Pleasant', 'Rock Hill', 'Greenville', 'Summerville', 'Goose Creek', 'Hilton Head Island', 'Myrtle Beach'],
            'South Dakota' => ['Sioux Falls', 'Rapid City', 'Aberdeen', 'Brookings', 'Watertown', 'Mitchell', 'Yankton'],
            'Tennessee' => ['Nashville', 'Memphis', 'Knoxville', 'Chattanooga', 'Clarksville', 'Murfreesboro', 'Franklin', 'Jackson', 'Johnson City'],
            'Texas' => ['Houston', 'San Antonio', 'Dallas', 'Austin', 'Fort Worth', 'El Paso', 'Arlington', 'Corpus Christi', 'Plano', 'Lubbock', 'Laredo', 'Irving', 'Garland', 'Frisco', 'McKinney', 'Amarillo', 'Grand Prairie', 'Brownsville', 'Killeen', 'Pasadena', 'Mesquite', 'McAllen', 'Denton', 'Waco', 'Carrollton', 'Round Rock', 'Abilene', 'Woodlands', 'Galveston', 'South Padre Island'],
            'Utah' => ['Salt Lake City', 'West Valley City', 'Provo', 'West Jordan', 'Orem', 'Sandy', 'Ogden', 'St. George', 'Layton'],
            'Vermont' => ['Burlington', 'South Burlington', 'Rutland', 'Barre', 'Montpelier', 'Winooski', 'St. Albans'],
            'Virginia' => ['Virginia Beach', 'Chesapeake', 'Norfolk', 'Richmond', 'Newport News', 'Alexandria', 'Hampton', 'Roanoke', 'Portsmouth', 'Suffolk', 'Lynchburg', 'Arlington', 'McLean'],
            'Washington' => ['Seattle', 'Spokane', 'Tacoma', 'Vancouver', 'Bellevue', 'Kent', 'Everett', 'Renton', 'Spokane Valley', 'Federal Way', 'Yakima', 'Bellingham'],
            'West Virginia' => ['Charleston', 'Huntington', 'Morgantown', 'Parkersburg', 'Wheeling', 'Weirton', 'Fairmont', 'Martinsburg'],
            'Wisconsin' => ['Milwaukee', 'Madison', 'Green Bay', 'Kenosha', 'Racine', 'Appleton', 'Waukesha', 'Oshkosh', 'Eau Claire', 'Janesville'],
            'Wyoming' => ['Cheyenne', 'Casper', 'Laramie', 'Gillette', 'Rock Springs', 'Sheridan', 'Green River', 'Jackson']
        ];
    }

    /**
     * Get list of state names sorted alphabetically.
     *
     * @return array<int, string>
     */
    public static function getStates(): array
    {
        $states = array_keys(self::getStatesAndCities());
        sort($states);
        return $states;
    }
}
