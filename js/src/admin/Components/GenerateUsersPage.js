import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';

export default class GenerateUsersPage extends Component {
  oninit(vnode) {
    super.oninit(vnode);
    this.loading = false;
    this.count = 1;
  }

  view() {
    return (
      <div className="GenerateUsersPage">
        <div className="container">
          <h2>Generate Users</h2>
          <div className="Form">
            <div className="Form-group">
              <label>Number of users to generate:</label>
              <input
                type="number"
                value={this.count}
                onChange={(e) => this.count = parseInt(e.target.value)}
                min="1"
                max="100"
              />
            </div>
            <Button
              className="Button Button--primary"
              disabled={this.loading}
              onclick={() => this.generateUsers()}
            >
              {this.loading ? 'Generating...' : 'Generate Users'}
            </Button>
          </div>
        </div>
      </div>
    );
  }

  generateUsers() {
    this.loading = true;

    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/generate-users',
      body: { count: this.count }
    }).then(response => {
      app.alerts.show(
        { type: 'success' },
        app.translator.trans('your-vendor-generate-users.admin.users_generated', {
          count: response.users.length
        })
      );
    }).catch(error => {
      app.alerts.show(
        { type: 'error' },
        'Error generating users: ' + error.message
      );
    }).finally(() => {
      this.loading = false;
      m.redraw();
    });
  }
}
