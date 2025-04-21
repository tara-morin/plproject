// js/home.js
(function() {
  'use strict';

  // Wait for DOM
  $(function() {
    const $lb = $('#leaderboard');
    if (!$lb.length) return;  // only run on home.php

    // 1) Fetch & render the leaderboard
    $.getJSON('index.php?command=getLeaderboardJson')
      .done(data => {
        $lb.empty();
        data.forEach((row, idx) => {
          let medal, badgeCls;
          if (idx === 0) {
            medal = '🥇'; badgeCls = 'bg-warning text-dark';
          } else if (idx === 1) {
            medal = '🥈'; badgeCls = 'bg-secondary';
          } else if (idx === 2) {
            medal = '🥉'; badgeCls = 'third';
          } else {
            medal = `#${idx+1}`; badgeCls = 'bg-light text-dark';
          }

          const h = Math.floor(row.total_minutes/60);
          const m = row.total_minutes % 60;
          const duration = [ h && `${h} hr`, m && `${m} min` ]
                             .filter(Boolean)
                             .join(' ') || '0 min';

          $lb.append(`
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>${medal} <strong>${idx+1}${idx<3?['st','nd','rd'][idx]:'th'} Place</strong></span>
              <span class="username">@${row.username}</span>
              <span class="badge ${badgeCls}">${duration}</span>
            </li>
          `);
        });

        // 2) Highlight current user
        const me = $('body').data('username');
        $lb.find('li').each((_, li) => {
          if ($(li).find('.username').text().trim() === `@${me}`) {
            $(li).addClass('list-group-item-primary');
          }
        });

        // 3) Add tooltip showing study time
        $lb.find('.list-group-item').tooltip({
          title: function() {
            return $(this).find('.badge').text() + ' studied';
          },
          placement: 'right'
        });
      })
      .fail(() => {
        $lb.html('<li class="list-group-item text-center text-danger">Could not load leaderboard.</li>');
      });

    // 4) Confirm before starting focus session
    $('#focusBtn').on('click', e => {
      const task = $(e.currentTarget).data('task-title');
      if (!confirm(`Start focus session for “${task}”?`)) {
        e.preventDefault();
      }
    });
  });
})();

  