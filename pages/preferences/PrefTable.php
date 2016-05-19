<div id="prefTable"></div>
<script type="text/babel">
var ctitles = <?php echo json_encode($ctitles); ?>;
var rtitles = <?php echo json_encode($rtitles); ?>;

var Table = React.createClass({
  getInitialState: function() {
    var colorArray = <?php echo json_encode($colorArray); ?>;
    return {color: colorArray};
  },

  handleMouse: function(row, column) {
    var colorArray = this.state.color;

    if (row == null && column == null) {
      for (var i = 0; i < rtitles.length; i++) {
        for (var j = 0; j < ctitles.length; j++) {
          if (colorArray[i][j] == 'green') {colorArray[i][j] = 'greenOutline';}
          else if (colorArray[i][j] == 'greenOutline') {colorArray[i][j] = 'green';}
          else if (colorArray[i][j] == 'red') {colorArray[i][j] = 'redOutline';}
          else if (colorArray[i][j] == 'redOutline') {colorArray[i][j] = 'red';}
        }
      }
    } else if (row == null) {
      for (var i = 0; i < rtitles.length; i++) {
        if (colorArray[i][column] == 'green') {colorArray[i][column] = 'greenOutline';}
        else if (colorArray[i][column] == 'greenOutline') {colorArray[i][column] = 'green';}
        else if (colorArray[i][column] == 'red') {colorArray[i][column] = 'redOutline';}
        else if (colorArray[i][column] == 'redOutline') {colorArray[i][column] = 'red';}
      }
    } else if (column == null) {
      for (var j = 0; j < ctitles.length; j++) {
        if (colorArray[row][j] == 'green') {colorArray[row][j] = 'greenOutline';}
        else if (colorArray[row][j] == 'greenOutline') {colorArray[row][j] = 'green';}
        else if (colorArray[row][j] == 'red') {colorArray[row][j] = 'redOutline';}
        else if (colorArray[row][j] == 'redOutline') {colorArray[row][j] = 'red';}
      }
    }

    this.setState({color: colorArray});
  },

  handleClick: function(row, column) {
    var colorArray = this.state.color;

    var countGreen = 0;
    var countRed = 0;
    if (row == null && column == null) {
      for (var i = 0; i < rtitles.length; i++) {
        for (var j = 0; j < ctitles.length; j++) {
          if (colorArray[i][j] == 'greenOutline') countGreen++;
          if (colorArray[i][j] == 'redOutline') countRed++;
        }
      }
    } else if (row == null) {
      for (var i = 0; i < rtitles.length; i++) {
        if (colorArray[i][column] == 'greenOutline') countGreen++;
        if (colorArray[i][column] == 'redOutline') countRed++;
      }
    } else if (column == null) {
      for (var j = 0; j < ctitles.length; j++) {
        if (colorArray[row][j] == 'greenOutline') countGreen++;
        if (colorArray[row][j] == 'redOutline') countRed++;
      }
    } else {
      if (colorArray[row][column] == 'green') countGreen++;
      if (colorArray[row][column] == 'red') countRed++;
    }

    var colormax = '';
    if (countGreen > countRed) {
      colormax = 'green';
    } else {
      colormax = 'red';
    }

    if (row == null && column == null) {
      for (var i = 0; i < rtitles.length; i++) {
        for (var j = 0; j < ctitles.length; j++) {
          if (colorArray[i][j] != 'gray') {
            if (colormax == 'green') {colorArray[i][j] = 'redOutline';}
            else if (colormax == 'red') {colorArray[i][j] = 'greenOutline';}
          }
        }
      }
    } else if (row == null) {
      for (var i = 0; i < rtitles.length; i++) {
        if (colorArray[i][column] != 'gray') {
          if (colormax == 'green') {colorArray[i][column] = 'redOutline';}
          else if (colormax == 'red') {colorArray[i][column] = 'greenOutline';}
        }
      }
    } else if (column == null) {
    for (var j = 0; j < ctitles.length; j++) {
        if (colorArray[row][j] != 'gray') {
          if (colormax == 'green') {colorArray[row][j] = 'redOutline';}
          else if (colormax == 'red') {colorArray[row][j] = 'greenOutline';}
        }
      }
    } else {
      if (colorArray[row][column] != 'gray') {
        if (colormax == 'green') {colorArray[row][column] = 'red';}
        else if (colormax == 'red') {colorArray[row][column] = 'green';}
      }
    }

    this.setState({color: colorArray});
  },

  render: function() {
    var ctitles = this.props.ctitles,
        rtitles = this.props.rtitles,
        color = this.state.color,
        handleMouse = this.handleMouse,
        handleClick = this.handleClick;

    var prefs = {};
    for (var i = 0; i < rtitles.length; i++) {
      prefs[i] = '';
      for (var j = 0; j < ctitles.length; j++) {
        if (color[i][j] == 'gray') {prefs[i] += '0'}
        else if (color[i][j] == 'red' || color[i][j] == 'redOutline') {prefs[i] += '1'}
        else if (color[i][j] == 'green' || color[i][j] == 'greenOutline') {prefs[i] += '2'}
      }
    }

    return (
      <table className="prefs">
        <tbody>
          <tr>
            <td rowSpan="2" className="blue center" onMouseOver={handleMouse.bind(null, null, null)} onMouseOut={handleMouse.bind(null, null, null)}
                                                    onClick={handleClick.bind(null, null, null)}>ALL</td>
            <td colSpan="12" className="blue center">AM</td>
            <td colSpan="12" className="blue center">PM</td>
          </tr>
          <tr>
            {
              ctitles.map(function(column, j) {
                return <td key={j} className="blue" onMouseOver={handleMouse.bind(null, null, j)} onMouseOut={handleMouse.bind(null, null, j)}
                                                    onClick={handleClick.bind(null, null, j)}>{column}</td>;
              })
            }
          </tr>
          {
            rtitles.map(function(row, index) {
              var i = index;
              return (
                <tr key={i}>
                  <td className="blue" onMouseOver={handleMouse.bind(null, i, null)} onMouseOut={handleMouse.bind(null, i, null)}
                                       onClick={handleClick.bind(null, i, null)}>
                    {row}
                    <input type="hidden" name={"tprefs"+i} value={prefs[i]}/>
                  </td>
                  {
                    ctitles.map(function(cell, j) {
                      var symbol = '';
                      if (color[i][j] == 'green' || color[i][j] == 'greenOutline') {symbol = String.fromCharCode(0x2713);}
                      else if (color[i][j] == 'red' || color[i][j] == 'redOutline') {symbol = String.fromCharCode(0x2715);}
                      return <td key={j} className={color[i][j]} onClick={handleClick.bind(null, i, j)}>{symbol}</td>;
                    })
                  }
                </tr>
              );
            })
          }
        </tbody>
      </table>
    );
  }
});

ReactDOM.render(
  <Table ctitles={ctitles} rtitles={rtitles}/>,
  document.getElementById('prefTable')
);
</script>
