<div class="top-contents-center">
  <div class="top-contents-head text-center bg-dark text-white h4 font-alegreya">Tour Info</div>
  <div class="top-center-list-box top-tour-box">
    <table class="table table-striped m-0">
      <thead>
        <tr class="thead-dark">
          <th class="top-center-table-date-w text-center">Start Date</th>
          <th class="top-center-table-name-w text-center">Name</th>
          <th class="top-center-table-category-w text-center">Category</th>
        </tr>
      </thead>
    </table>
    <div class="table table-striped top-center-tbody mb-0">
      <table>
        <tbody class="w-100">
          @if( !empty( $tour_informations) )
            @foreach ( $tour_informations as $tour )
              @if ( $tour['start_date'] <= $today && $tour['end_date'] >= $today )
                <tr>
                  <td class="top-contents-center-td top-center-table-date-w text-center text-info font-weight-bold font-14">{{ $tour['start_date'] }}</td>
                  <td class="top-contents-center-td top-center-table-name-w text-center text-info font-weight-bold font-14">{{ $tour['name'] }}</td>
                  <td class="top-contents-center-td top-center-table-category-w text-info font-weight-bold font-14">{{ $tour['category'] }}</td>
                </tr>
              @else
                <tr>
                  <td class="top-contents-center-td top-center-table-date-w text-center font-14">{{ $tour['start_date'] }}</td>
                  <td class="top-contents-center-td top-center-table-name-w text-center font-14">{{ $tour['name'] }}</td>
                  <td class="top-contents-center-td top-center-table-category-w font-14">{{ $tour['category'] }}</td>
                </tr>
              @endif
            @endforeach
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="top-contents-center mt-3">
  <div class="top-contents-head text-center bg-dark text-white h4 font-alegreya">Movie</div>
  <div class="top-center-list-box top-movie-box">
    <iframe width="100%" height="250" src="https://www.youtube.com/embed/MfEGqgZffUo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <iframe width="100%" height="250" src="https://www.youtube.com/embed/MfEGqgZffUo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <iframe width="100%" height="250" src="https://www.youtube.com/embed/MfEGqgZffUo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
  </div>
</div>